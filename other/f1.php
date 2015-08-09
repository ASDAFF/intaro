<?
/*
	Данный код отвечает за отправку post запроса при добавлении товара в wishlist
	
	я его разместил в файле шаблона детального просмотра товара: \Bitrix\www\bitrix\components\bitrix\catalog\templates\mydefault\bitrix\catalog.element\.default\template.php
*/
?>


		<div id="btnWishlist" style="padding:10px 0px;">
<?
			$id_tovar = (int)$arResult['ID'] * 1;
			$id_user  = (int)$USER -> GetID()* 1;
			// Если товата нет в wishlist'е пользователя 
			if(!Wishlist::ExistTovar($id_tovar, $id_user)){
				echo '<a onclick="SendPost('. $id_tovar .');" class="bx_big bx_bt_button">+ Добавить в wishlist</a>';
			}else{
				echo '<a href="/wishlist/" class="bx_big bx_bt_button_type_2">Товар добавлен в wishlist</a>';
			}
?>
		</div>


<script type="text/javascript">
  function getXmlHttp() {
    var xmlhttp;
    try {
      xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
    try {
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    } catch (E) {
      xmlhttp = false;
    }
    }
    if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
      xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
  }
  function SendPost(id) {

    var xmlhttp = getXmlHttp(); // Создаём объект XMLHTTP
	
    xmlhttp.open('POST', '/wishlist/a.php', true); // Открываем асинхронное соединение
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); // Отправляем кодировку
    xmlhttp.send("id=" + encodeURIComponent(id)); // Отправляем POST-запрос
	
    xmlhttp.onreadystatechange = function() { // Ждём ответа от сервера
      if (xmlhttp.readyState == 4) { // Ответ пришёл
        if(xmlhttp.status == 200) { // Сервер вернул код 200 (что хорошо)
          if(xmlhttp.responseText == '1'){ // Выводим ответ сервера
			document.getElementById("btnWishlist").innerHTML = '<a href="/wishlist/" class="bx_big bx_bt_button_type_2">Товар добавлен в wishlist</a>';
		  }else{
			alert('Ошибка: ' + xmlhttp.responseText);
		  }
        }
      }
    };
  }
</script>