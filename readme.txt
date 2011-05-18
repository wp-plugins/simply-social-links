=== Simply Social Links ===
Contributors: matheuseduardo
Donate link: http://maathe.us/blog/me-paga-um-cafe/
Tags: simple, links, social, network, 
Requires at least: 3.0
Tested up to: 3.1.2
Stable tag: 0.6
A simple way to add social links (like: facebook, twitter, tumblr, last.fm, flickr, plurk, etc) to your Links (Bookmarks?!).


== Description ==
This plugin adds a new field (metadata) in the section editing links in order to add social networking links (like [twitter](http://twitter.com/), [flickr](http://flickr.com/), etc.) to any link.

=PT-BR=
Este plugin acrescenta um novo campo (metadados) na seção de edição de links para poder adicionars links de redes sociais (como [twitter](http://twitter.com/), [flickr](http://flickr.com/), etc) a um link qualquer.


== Installation ==
1. Upload the 'Simply Social Links' folder to the plugins directory in your WordPress installation
2. Activate the plugin
3. Go to Link management and try editing an existing 'bookmark'
4. Use `wp_list_bookmaks_ssl($params)` instead `wp_list_bookmaks($params)` in your theme files
5. [SOON] Use "Links SSL" Widget instead "Links" do Wordpress
6. [SOON] In 'Options' tab configure the plugin

= PT-BR =
1. Faça upload dos arquivos para a pasta `/wp-content/plugins/`  (mantenha a pasta original do plugin)
2. Ative o plugin na interface de 'Plugins' do WordPress
3. Acesse o gerenciamento de links e teste editando um link já existente
4. Use `wp_list_bookmaks_ssl($params)` ao invés de `wp_list_bookmaks($params)` nos seus arquivos do tema
5. [SOON] Use o Widget "Links SSL" ao invés do "Links" do WP
6. [SOON] Na Aba de opções configure o plugin


== Frequently Asked Questions ==
= What happen if I write a wrong url =
 A: It will be wrong. There is no validator (till now).

= Any possibility of including other social networks? =
 R: According the need and/or suggestions.
 
~~ PT-BR ~~

= E se eu preencher um link incorreto? =
 R: Vai ficar incorreto. Não existe ainda um validador.

= Possibilidade de incluir outras redes sociais? =
 R: Conforme surgir necessidade/sugestões.

 
== Screenshots ==
1. Link manager / Gerenciador de Links
 
== Changelog ==
= 0.6 =
* use the function `wp_list_bookmarks_ssl()` (instead of usual `wp_list_bookmarks()`) to get your bookmarks with 'social links' attached
= 0.5 =
* First Public Release
 
== Upgrade Notice ==
[SOON] new update to use with shortcodes in pages, post, etc and better instructions of how to style according to your taste
= 0.6 =
* use the function `wp_list_bookmarks_ssl()` (instead of usual `wp_list_bookmarks()`) to get your bookmarks with 'social links' attached
= 0.5 =
* First Public Release
