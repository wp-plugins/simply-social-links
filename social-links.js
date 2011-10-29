// JavaScript Document

jQuery(document).ready(function() {
	jQuery('select#ssl_network_site').change(function(a){
		var rede = this.value;
		jQuery('input#ssl_network_url').removeClass().addClass(rede).focus();
	});
	
	jQuery('select#ssl_network_site').change();
	
	jQuery('a.incluir').click(ssl_add_link);
	
	if (typeof jQuery.fn.hasAttr != "function") {
		jQuery.fn.hasAttr = function(name) {
			return typeof this.attr(name) != "undefined";
		};
	}
	

	
});

function ssl_add_link() {
	// console.log('clicou');
	var site = jQuery('select#ssl_network_site').val();
	var url = jQuery('input#ssl_network_url').val();
	var link_id = jQuery('input[name=link_id]').val()
	
	jQuery('select#ssl_network_site, input#ssl_network_url').attr('disabled','disabled');
	
	var data = {
		action: 'add_social_network_link',
		site: site,
		url: url,
		link_id : link_id
	};
	
	var jqxhr = jQuery.ajax({
		url: ajaxurl,
		dataType: 'json',
		data: data
	})
	.success(function(a) {
		console.log('sucesso');
		if (a.sucesso=="S") {
			var but_del = " <a href=\"javascript:void(0);\" class=\"button-secondary hide-if-no-js \" onclick=\"ssl_delete_link(event, '" + a.link.mid + "','" + link_id + "')\">" + SocialLinks.delete + "</a>"
			var but_vis = " <a href=\"" + url + "\" target=\"_blank\" class=\"button-secondary hide-if-no-js \">" + SocialLinks.visit + "</a>";
			var el = '<dt class="' + site + '"></dt><dd>' + url + but_del + but_vis + '</dd>';
			jQuery('#ssl_links_list').append(el);
			jQuery('input#ssl_network_url').val('http://');
		}
		else if(a.sucesso=="N") {
			alert(a.mensagem)
		}
		
	})
	.error(function(c, d) {
		alert(SocialLinks.error + ": " + c.status + " - " + c.statusText);
	})
	.complete(function(e) {
		jQuery('select#ssl_network_site, input#ssl_network_url').removeAttr('disabled');
	});
	
	return true;
	
}

function ssl_delete_link(event, mid, lid) {
	
	var src = jQuery(event.target).parent();
	var ant = src.prev();

	
	var data = {
		action: 'delete_social_network_link',
		meta_id: mid,
		link_id: lid
	};
	
	var jqxhr = jQuery.ajax({
		url: ajaxurl,
		dataType: 'json',
		data: data
	})
	.success(function(a) {
		console.log('sucesso');
		// console.log(a);
		src.remove();
		ant.remove();
	})
	.error(function(c, d) {
		alert("Erro: " + c.status + " - " + c.statusText);
	})
	.complete(function(e) {
		console.log('complete');
		// console.log(e);
		jQuery('select#ssl_network_site, input#ssl_network_url').removeAttr('disabled');
	});
	
	return true;
	
}














































