<?php

/**
 * The WordPress importer plugin has a few issues that break
 * serialized data in certain cases. This class is our own
 * patched version that fixes these issues.
 *
 * @since 1.8
 */
class FLBuilderImporter extends WP_Import {

	/**
	 * @since 1.8
	 * @return array
	 */
	function parse( $file ) {

		if ( extension_loaded( 'simplexml' ) ) {
			$parser = new FLBuilderImportParserSimpleXML;
			$result = $parser->parse( $file );

				// If SimpleXML succeeds or this is an invalid WXR file then return the results
			if ( ! is_wp_error( $result ) || 'SimpleXML_parse_error' != $result->get_error_code() ) {
				return $result;
			}
		} elseif ( extension_loaded( 'xml' ) ) {

			$parser = new FLBuilderImportParserXML();
			$result = $parser->parse( $file );
			if ( ! is_wp_error( $result ) || 'SimpleXML_parse_error' != $result->get_error_code() ) {
				return $result;
			}
		}
		// We have a malformed XML file, so display the error and fallthrough to regex
		if ( isset( $result ) && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			echo '<pre>';
			if ( 'SimpleXML_parse_error' == $result->get_error_code() ) {
				foreach ( $result->get_error_data() as $error ) {
					echo $error->line . ':' . $error->column . ' ' . esc_html( $error->message ) . "\n";
				}
			} elseif ( 'XML_parse_error' == $result->get_error_code() ) {
				$error = $result->get_error_data();
				echo $error[0] . ':' . $error[1] . ' ' . esc_html( $error[2] );
			}
			$data = file_get_contents( $file );
			$bad  = preg_match( '#[^\x00-\x7F]#', $data );
			if ( $bad ) {
					echo __( 'Some bad characters were found in the xml file', 'fl-builder' );
			}
			echo '</pre>';
			echo '<p><strong>' . __( 'There was an error when reading this WXR file', 'wordpress-importer' ) . '</strong><br />';
			echo '<p>' . __( 'Details are shown above. The importer will now try again with a different parser...', 'wordpress-importer' ) . '</p>';
		}
		$parser = new FLBuilderImportParserRegex();
		return $parser->parse( $file );
	}
}

class FLBuilderImportParserSimpleXML extends WXR_Parser_SimpleXML {
	function parse( $file ) {

		$authors    = array();
		$posts      = array();
		$categories = array();
		$tags       = array();
		$terms      = array();

		$internal_errors = libxml_use_internal_errors( true );

		$dom       = new DOMDocument;
		$old_value = null;
		if ( function_exists( 'libxml_disable_entity_loader' ) ) {
			$old_value = libxml_disable_entity_loader( true );
		}
		$success = $dom->loadXML( file_get_contents( $file ) );
		if ( ! is_null( $old_value ) ) {
			libxml_disable_entity_loader( $old_value );
		}

		if ( ! $success || isset( $dom->doctype ) ) {
			return new WP_Error( 'SimpleXML_parse_error', __( 'There was an error when reading this WXR file', 'wordpress-importer' ), libxml_get_errors() );
		}

		$xml = simplexml_import_dom( $dom );
		unset( $dom );

		// halt if loading produces an error
		if ( ! $xml ) {
			return new WP_Error( 'SimpleXML_parse_error', __( 'There was an error when reading this WXR file', 'wordpress-importer' ), libxml_get_errors() );
		}

		$wxr_version = $xml->xpath( '/rss/channel/wp:wxr_version' );
		if ( ! $wxr_version ) {
			return new WP_Error( 'WXR_parse_error', __( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'wordpress-importer' ) );
		}

		$wxr_version = (string) trim( $wxr_version[0] );
		// confirm that we are dealing with the correct file format
		if ( ! preg_match( '/^\d+\.\d+$/', $wxr_version ) ) {
			return new WP_Error( 'WXR_parse_error', __( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'wordpress-importer' ) );
		}

		$base_url = $xml->xpath( '/rss/channel/wp:base_site_url' );
		$base_url = (string) trim( $base_url[0] );

		$base_blog_url = $xml->xpath( '/rss/channel/wp:base_blog_url' );
		if ( $base_blog_url ) {
			$base_blog_url = (string) trim( $base_blog_url[0] );
		} else {
			$base_blog_url = $base_url;
		}

		$namespaces = $xml->getDocNamespaces();
		if ( ! isset( $namespaces['wp'] ) ) {
			$namespaces['wp'] = 'http://wordpress.org/export/1.1/';
		}
		if ( ! isset( $namespaces['excerpt'] ) ) {
			$namespaces['excerpt'] = 'http://wordpress.org/export/1.1/excerpt/';
		}

		// grab authors
		foreach ( $xml->xpath( '/rss/channel/wp:author' ) as $author_arr ) {
			$a                 = $author_arr->children( $namespaces['wp'] );
			$login             = (string) $a->author_login;
			$authors[ $login ] = array(
				'author_id'           => (int) $a->author_id,
				'author_login'        => $login,
				'author_email'        => (string) $a->author_email,
				'author_display_name' => (string) $a->author_display_name,
				'author_first_name'   => (string) $a->author_first_name,
				'author_last_name'    => (string) $a->author_last_name,
			);
		}

		// grab cats, tags and terms
		foreach ( $xml->xpath( '/rss/channel/wp:category' ) as $term_arr ) {
			$t        = $term_arr->children( $namespaces['wp'] );
			$category = array(
				'term_id'              => (int) $t->term_id,
				'category_nicename'    => (string) $t->category_nicename,
				'category_parent'      => (string) $t->category_parent,
				'cat_name'             => (string) $t->cat_name,
				'category_description' => (string) $t->category_description,
			);

			foreach ( $t->termmeta as $meta ) {
				$category['termmeta'][] = array(
					'key'   => (string) $meta->meta_key,
					'value' => (string) $meta->meta_value,
				);
			}

			$categories[] = $category;
		}

		foreach ( $xml->xpath( '/rss/channel/wp:tag' ) as $term_arr ) {
			$t   = $term_arr->children( $namespaces['wp'] );
			$tag = array(
				'term_id'         => (int) $t->term_id,
				'tag_slug'        => (string) $t->tag_slug,
				'tag_name'        => (string) $t->tag_name,
				'tag_description' => (string) $t->tag_description,
			);

			foreach ( $t->termmeta as $meta ) {
				$tag['termmeta'][] = array(
					'key'   => (string) $meta->meta_key,
					'value' => (string) $meta->meta_value,
				);
			}

			$tags[] = $tag;
		}

		foreach ( $xml->xpath( '/rss/channel/wp:term' ) as $term_arr ) {
			$t    = $term_arr->children( $namespaces['wp'] );
			$term = array(
				'term_id'          => (int) $t->term_id,
				'term_taxonomy'    => (string) $t->term_taxonomy,
				'slug'             => (string) $t->term_slug,
				'term_parent'      => (string) $t->term_parent,
				'term_name'        => (string) $t->term_name,
				'term_description' => (string) $t->term_description,
			);

			foreach ( $t->termmeta as $meta ) {
				$term['termmeta'][] = array(
					'key'   => (string) $meta->meta_key,
					'value' => (string) $meta->meta_value,
				);
			}

			$terms[] = $term;
		}

		// grab posts
		foreach ( $xml->channel->item as $item ) {
			$post = array(
				'post_title' => (string) $item->title,
				'guid'       => (string) $item->guid,
			);

			$dc                  = $item->children( 'http://purl.org/dc/elements/1.1/' );
			$post['post_author'] = (string) $dc->creator;

			$content              = $item->children( 'http://purl.org/rss/1.0/modules/content/' );
			$excerpt              = $item->children( $namespaces['excerpt'] );
			$post['post_content'] = (string) $content->encoded;
			$post['post_excerpt'] = (string) $excerpt->encoded;

			$wp                     = $item->children( $namespaces['wp'] );
			$post['post_id']        = (int) $wp->post_id;
			$post['post_date']      = (string) $wp->post_date;
			$post['post_date_gmt']  = (string) $wp->post_date_gmt;
			$post['comment_status'] = (string) $wp->comment_status;
			$post['ping_status']    = (string) $wp->ping_status;
			$post['post_name']      = (string) $wp->post_name;
			$post['status']         = (string) $wp->status;
			$post['post_parent']    = (int) $wp->post_parent;
			$post['menu_order']     = (int) $wp->menu_order;
			$post['post_type']      = (string) $wp->post_type;
			$post['post_password']  = (string) $wp->post_password;
			$post['is_sticky']      = (int) $wp->is_sticky;

			if ( isset( $wp->attachment_url ) ) {
				$post['attachment_url'] = (string) $wp->attachment_url;
			}

			foreach ( $item->category as $c ) {
				$att = $c->attributes();
				if ( isset( $att['nicename'] ) ) {
					$post['terms'][] = array(
						'name'   => (string) $c,
						'slug'   => (string) $att['nicename'],
						'domain' => (string) $att['domain'],
					);
				}
			}

			foreach ( $wp->postmeta as $meta ) {
				FLBuilderImporterDataFix::set_pcre_limit( apply_filters( 'fl_builder_importer_pcre', '23001337' ) );

				if ( '_fl_builder_data' == $meta->meta_key ) {
					$data = FLBuilderImporterDataFix::run( (string) $meta->meta_value );
					if ( is_object( $data ) || is_array( $data ) ) {
						$data = serialize( $data );
					}
				} else {
					$data = $meta->meta_value;
				}

				$post['postmeta'][] = array(
					'key'   => (string) $meta->meta_key,
					'value' => (string) $data,
				);
			}

			foreach ( $wp->comment as $comment ) {
				$meta = array();
				if ( isset( $comment->commentmeta ) ) {
					foreach ( $comment->commentmeta as $m ) {
						$meta[] = array(
							'key'   => (string) $m->meta_key,
							'value' => (string) $m->meta_value,
						);
					}
				}

				$post['comments'][] = array(
					'comment_id'           => (int) $comment->comment_id,
					'comment_author'       => (string) $comment->comment_author,
					'comment_author_email' => (string) $comment->comment_author_email,
					'comment_author_IP'    => (string) $comment->comment_author_IP,
					'comment_author_url'   => (string) $comment->comment_author_url,
					'comment_date'         => (string) $comment->comment_date,
					'comment_date_gmt'     => (string) $comment->comment_date_gmt,
					'comment_content'      => (string) $comment->comment_content,
					'comment_approved'     => (string) $comment->comment_approved,
					'comment_type'         => (string) $comment->comment_type,
					'comment_parent'       => (string) $comment->comment_parent,
					'comment_user_id'      => (int) $comment->comment_user_id,
					'commentmeta'          => $meta,
				);
			}

			$posts[] = $post;
		}

		return array(
			'authors'       => $authors,
			'posts'         => $posts,
			'categories'    => $categories,
			'tags'          => $tags,
			'terms'         => $terms,
			'base_url'      => $base_url,
			'base_blog_url' => $base_blog_url,
			'version'       => $wxr_version,
		);
	}
}

class FLBuilderImportParserXML extends WXR_Parser_XML {

	function tag_close( $parser, $tag ) {
		switch ( $tag ) {
			case 'wp:comment':
				unset( $this->sub_data['key'], $this->sub_data['value'] ); // remove meta sub_data
				if ( ! empty( $this->sub_data ) ) {
					$this->data['comments'][] = $this->sub_data;
				}
				$this->sub_data = false;
				break;
			case 'wp:commentmeta':
				$this->sub_data['commentmeta'][] = array(
					'key'   => $this->sub_data['key'],
					'value' => $this->sub_data['value'],
				);
				break;
			case 'category':
				if ( ! empty( $this->sub_data ) ) {
					$this->sub_data['name'] = $this->cdata;
					$this->data['terms'][]  = $this->sub_data;
				}
				$this->sub_data = false;
				break;
			case 'wp:postmeta':
				if ( ! empty( $this->sub_data ) ) {
					if ( stristr( $this->sub_data['key'], '_fl_builder_' ) ) {
						FLBuilderImporterDataFix::set_pcre_limit( apply_filters( 'fl_builder_importer_pcre', '23001337' ) );
						$data = FLBuilderImporterDataFix::run( $this->sub_data['value'] );
						if ( is_object( $data ) || is_array( $data ) ) {
							$data = serialize( $data );
						}
						$this->sub_data['value'] = $data;
					}
					$this->data['postmeta'][] = $this->sub_data;
				}
				$this->sub_data = false;
				break;
			case 'item':
				$this->posts[] = $this->data;
				$this->data    = false;
				break;
			case 'wp:category':
			case 'wp:tag':
			case 'wp:term':
				$n = substr( $tag, 3 );
				array_push( $this->$n, $this->data );
				$this->data = false;
				break;
			case 'wp:author':
				if ( ! empty( $this->data['author_login'] ) ) {
					$this->authors[ $this->data['author_login'] ] = $this->data;
				}
				$this->data = false;
				break;
			case 'wp:base_site_url':
				$this->base_url = $this->cdata;
				break;
			case 'wp:wxr_version':
				$this->wxr_version = $this->cdata;
				break;
			default:
				if ( $this->in_sub_tag ) {
					$this->sub_data[ $this->in_sub_tag ] = ! empty( $this->cdata ) ? $this->cdata : '';
					$this->in_sub_tag                    = false;
				} elseif ( $this->in_tag ) {
					$this->data[ $this->in_tag ] = ! empty( $this->cdata ) ? $this->cdata : '';
					$this->in_tag                = false;
				}
		}
		$this->cdata = false;
	}
}


/**
 * The Regex parser is the only parser we have found that
 * doesn't break serialized data. It does have two bugs
 * that can break serialized data. Those are calling rtrim
 * on each $importline and adding a newline to each $importline.
 * This class fixes those bugs.
 *
 * @since 1.8
 */
class FLBuilderImportParserRegex extends WXR_Parser_Regex {

	/**
	 * @since 1.8
	 * @return array
	 */
	function parse( $file ) {

		// @codingStandardsIgnoreLine
		$wxr_version = $in_post = false;

		$fp = $this->fopen( $file, 'r' );
		if ( $fp ) {
			while ( ! $this->feof( $fp ) ) {
				$importline = $this->fgets( $fp );

				if ( ! $wxr_version && preg_match( '|<wp:wxr_version>(\d+\.\d+)</wp:wxr_version>|', $importline, $version ) ) {
					$wxr_version = $version[1];
				}

				if ( false !== strpos( $importline, '<wp:base_site_url>' ) ) {
					preg_match( '|<wp:base_site_url>(.*?)</wp:base_site_url>|is', $importline, $url );
					$this->base_url = $url[1];
					continue;
				}
				if ( false !== strpos( $importline, '<wp:category>' ) ) {
					preg_match( '|<wp:category>(.*?)</wp:category>|is', $importline, $category );
					if ( isset( $category[1] ) ) {
						$this->categories[] = $this->process_category( $category[1] );
					}
					continue;
				}
				if ( false !== strpos( $importline, '<wp:tag>' ) ) {
					preg_match( '|<wp:tag>(.*?)</wp:tag>|is', $importline, $tag );
					if ( isset( $tag[1] ) ) {
						$this->tags[] = $this->process_tag( $tag[1] );
					}
					continue;
				}
				if ( false !== strpos( $importline, '<wp:term>' ) ) {
					preg_match( '|<wp:term>(.*?)</wp:term>|is', $importline, $term );
					if ( isset( $term[1] ) ) {
						$this->terms[] = $this->process_term( $term[1] );
					}
					continue;
				}
				if ( false !== strpos( $importline, '<wp:author>' ) ) {
					preg_match( '|<wp:author>(.*?)</wp:author>|is', $importline, $author );
					if ( isset( $author[1] ) ) {
						$a = $this->process_author( $author[1] );
					}
					$this->authors[ $a['author_login'] ] = $a;
					continue;
				}
				if ( false !== strpos( $importline, '<item>' ) ) {
					$post    = '';
					$in_post = true;
					continue;
				}
				if ( false !== strpos( $importline, '</item>' ) ) {
					$in_post = false;

					FLBuilderImporterDataFix::set_pcre_limit( apply_filters( 'fl_builder_importer_pcre', '23001337' ) );
					$this->posts[] = $this->process_post( $post );
					continue;
				}
				if ( $in_post ) {
					$post .= $importline;
				}
			}

			$this->fclose( $fp );

			// Try to fix any broken builder data.
			foreach ( $this->posts as $post_index => $post ) {
				if ( ! isset( $post['postmeta'] ) || ! is_array( $post['postmeta'] ) ) {
					continue;
				}
				foreach ( $post['postmeta'] as $postmeta_index => $postmeta ) {
					if ( stristr( $postmeta['key'], '_fl_builder_' ) ) {
						$data = FLBuilderImporterDataFix::run( $postmeta['value'] );
						if ( is_object( $data ) || is_array( $data ) ) {
							$data = serialize( $data );
						}
						$this->posts[ $post_index ]['postmeta'][ $postmeta_index ]['value'] = $data;
					}
				}
			}
		}

		if ( ! $wxr_version ) {
			return new WP_Error( 'WXR_parse_error', __( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'fl-builder' ) );
		}

		return array(
			'authors'    => $this->authors,
			'posts'      => $this->posts,
			'categories' => $this->categories,
			'tags'       => $this->tags,
			'terms'      => $this->terms,
			'base_url'   => $this->base_url,
			'version'    => $wxr_version,
		);
	}
}

/**
 * Portions borrowed from https://github.com/Blogestudio/Fix-Serialization/blob/master/fix-serialization.php
 *
 * Attempts to fix broken serialized data.
 *
 * @since 1.8
 */
final class FLBuilderImporterDataFix {

	/**
	 * @since 1.8
	 * @return string
	 */
	static public function run( $data ) {
		// return if empty
		if ( empty( $data ) ) {
			return $data;
		}

		if ( is_object( $data ) || is_array( $data ) ) {
			return $data;
		}

		if ( ! is_serialized( $data ) ) {
			return $data;
		}

		$data = preg_replace_callback('!s:(\d+):"(.*?)";!', function( $m ) {
			return 's:' . strlen( $m[2] ) . ':"' . $m[2] . '";';
		}, self::sanitize_from_word( $data ) );

		$data = maybe_unserialize( $data );

		// return if maybe_unserialize() returns an object or array, this is good.
		if ( is_object( $data ) || is_array( $data ) ) {
			return $data;
		}

		return preg_replace_callback( '!s:(\d+):([\\\\]?"[\\\\]?"|[\\\\]?"((.*?)[^\\\\])[\\\\]?");!', 'FLBuilderImporterDataFix::regex_callback', $data );
	}

	/**
	 * Remove quotes etc pasted from a certain word processor.
	 */
	public static function sanitize_from_word( $content ) {
		// Convert microsoft special characters
		$replace = array(
			'‘'  => "\'",
			'’'  => "\'",
			'”'  => '\"',
			'“'  => '\"',
			'–'  => '-',
			'—'  => '-',
			'…'  => '&#8230;',
			"\n" => '<br />',
		);

		foreach ( $replace as $k => $v ) {
			$content = str_replace( $k, $v, $content );
		}

		/**
		 * Optional strip all illegal chars, defaults to false
		 * @see fl_import_strip_all
		 * @since 2.3
		 */
		if ( true === apply_filters( 'fl_import_strip_all', false ) ) {
			// Remove any non-ascii character
			$content = preg_replace( '/[^\x20-\x7E]*/', '', $content );
		}

		return $content;
	}


	/**
	 * @since 1.8
	 * @return string
	 */
	static public function regex_callback( $matches ) {
		if ( ! isset( $matches[3] ) ) {
			return $matches[0];
		}

		return 's:' . strlen( self::unescape_mysql( $matches[3] ) ) . ':"' . self::unescape_quotes( $matches[3] ) . '";';
	}

	/**
	 * Unescape to avoid dump-text issues.
	 *
	 * @since 1.8
	 * @access private
	 * @return string
	 */
	static private function unescape_mysql( $value ) {
		return str_replace( array( '\\\\', "\\0", "\\n", "\\r", '\Z', "\'", '\"' ),
			array( '\\', "\0", "\n", "\r", "\x1a", "'", '"' ),
		$value );
	}

	/**
	 * Fix strange behaviour if you have escaped quotes in your replacement.
	 *
	 * @since 1.8
	 * @access private
	 * @return string
	 */
	static private function unescape_quotes( $value ) {
		return str_replace( '\"', '"', $value );
	}

	/**
	 * Try increasing PCRE limit to avoid failing of importing huge postmeta data.
	 *
	 * @since 1.10.9
	 * @param string $value
	 */
	static public function set_pcre_limit( $value ) {
		@ini_set( 'pcre.backtrack_limit', $value ); // @codingStandardsIgnoreLine
		@ini_set( 'pcre.recursion_limit', $value ); // @codingStandardsIgnoreLine
	}
}
