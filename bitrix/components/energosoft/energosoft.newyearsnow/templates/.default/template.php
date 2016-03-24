<?
######################################################
# Name: energosoft.newyearsnow                       #
# File: template.php                                 #
# (c) 2005-2011 Energosoft, Maksimov M.A.            #
# Dual licensed under the MIT and GPL                #
# http://energo-soft.ru/                             #
# mailto:support@energo-soft.ru                      #
######################################################
?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<script type="text/javascript">
jQuery(document).ready(function()
{
	snow_id=1;
	snow_y=jQuery(document).height()-30;
	snow_src=new Array(
		"<?=$templateFolder?>/images/snow1.png",
		"<?=$templateFolder?>/images/snow2.png",
		"<?=$templateFolder?>/images/snow3.png",
		"<?=$templateFolder?>/images/snow4.png"
	);
	setInterval(function()
	{
		snow_x=Math.random()*document.body.offsetWidth-100;
		snow_img=(snow_src instanceof Array ? snow_src[Math.floor(Math.random()*snow_src.length)] : snow_src);
		snow_elem='<img id="es-snow'+snow_id+'" style="position:absolute;left:'+snow_x+'px;top:0;z-index:10000;" src="'+snow_img+'"/>';
		jQuery(document.body).append(snow_elem);
		jQuery("#es-snow"+snow_id).animate({top:snow_y,left:"+="+Math.random()*100},<?=$arParams["ES_SPEED"]?>,function() { jQuery(this).empty().remove(); });
		snow_id++;
	},<?=$arParams["ES_INTENSIVE"]?>);
});
</script>