<? 
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("wishlist");

	$id_user  = (int)$USER -> GetID() * 1;
	// если пользователь существует
	if(!empty($id_user)){
		
		$page = 1; //номер страницы которую нужно открыть
		$countpost = 5;// Количество товаров которые показываются в списке на странице
		
		if(!empty($_GET["page"])) $page = (int) $_GET["page"] * 1;
		
		
		
		// Выбераем данные из Wishlist'а
		$arWishlist = Wishlist::GetWishlist($id_user, $countpost, $page);
		
		if(!empty($arWishlist)){
			// Формируем html
			$html = '<div><table width="100%">';
			foreach($arWishlist as $item){
				// Получаем данные о товаре из базы по его id
				$res = CIBlockElement::GetList(array(), array('ID'=>$item['id_tovar']), false, false, array('ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL'));
				// заносим в массив полученные данные
				$arElement = $res->GetNext();

				$arFilter = Array("IBLOCK_ID"=>$arElement[IBLOCK_ID], "ID"=>$arElement[ID]);
				$ress = CIBlockElement::GetList(Array(), $arFilter);
				if ($ob = $ress->GetNextElement()){ 
					$arFields = $ob->GetFields();
				}
				
				$html .= '<tr id="line'. $item['id_tovar'] .'">';
					// Картинка
					$html .= '<td style="background:#3b3937; padding:10;">';
						if(!empty($arFields['PREVIEW_PICTURE'])){
							$html .=  CFile::ShowImage($arFields['PREVIEW_PICTURE']); 
						}else{
							$html .=  '<img width="100%" src="/bitrix/components/bitrix/catalog/templates/mydefault/bitrix/catalog.element/.default/images/no_photo.png" width="68" />';
						}
					$html .= '</td>';
					// Ссылка
					$html .= '<td style="background:#42555b; padding:10; font-size:16px;" width="100%">';
					$html .=  "<a href='". $arElement['DETAIL_PAGE_URL'] ."'>". $arElement['NAME'] ."</a>";
					$html .= '</td>';
					// Действие
					$html .= '<td style="background:#3b3937; padding:10; color:#eee;">';
					$html .=  '<span onclick="SendPost('. $item['id_tovar'] .', '. $id_user .');" style="cursor:pointer;">Удалить</span>';
					$html .= '</td>';
				$html .= '</tr>';
			}
			$html .= '</table></div>';
			
			// формируем навигационные ссылки
			// получим количество новостей
			$countWishlist = Wishlist::CountWishlist($id_user);
			if($countWishlist > $countpost){
				$total = intval(($countWishlist - 1) / $countpost) + 1;// всего страниц
				$request_uri = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'],'?'));
				// Проверяем нужны ли стрелки назад
				if ($page != 1) $pervpage = '<a href="'.$request_uri.'?page=1" title="Страница 1"><<</a>
											 <a href="'.$request_uri.'?page='.($page - 1).'" title="Страница '.($page - 1).'"><</a> ';
				// Проверяем нужны ли стрелки вперед
				if ($page != $total) $nextpage = ' <a href="'.$request_uri.'?page='.($page + 1).'" title="Страница '.($page + 1).'">></a>
												  <a href="'.$request_uri.'?page='.$total.'" title="Страница '.$total.'">>></a>';
				// Находим две ближайшие станицы с обоих краев, если они есть
				if($page - 2 > 0) $page2left = '<a href="'.$request_uri.'?page='.($page - 2).'" title="Страница '.($page - 2).'">'.($page - 2).'</a> | ';
				if($page - 1 > 0) $page1left = '<a href="'.$request_uri.'?page='.($page - 1).'" title="Страница '.($page - 1).'">'.($page - 1).'</a> | ';
				if($page + 2 <= $total) $page2right = ' | <a href="'.$request_uri.'?page='.($page + 2).'" title="Страница '.($page + 2).'">'.($page + 2).'</a>';
				if($page + 1 <= $total) $page1right = ' | <a href="'.$request_uri.'?page='.($page + 1).'" title="Страница '.($page + 1).'">'.($page + 1).'</a>';

				// сохраняем навигационные ссылки
				$html .= '<div style="text-align:center;padding:10px;">'. $pervpage.$page2left.$page1left.$page.$page1right.$page2right.$nextpage .'</div>';
			}
		}else{
			$html = 'Ваш Wishlist пуст.';
		}
		echo $html;
	}
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
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
  function SendPost(id_tovar, id_user) {
    var xmlhttp = getXmlHttp(); // Создаём объект XMLHTTP
	
    xmlhttp.open('POST', 'a.php', true); // Открываем асинхронное соединение
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); // Отправляем кодировку
    xmlhttp.send("del=1&id_tovar=" + encodeURIComponent(id_tovar) + "&id_user=" + encodeURIComponent(id_user)); // Отправляем POST-запрос
    xmlhttp.onreadystatechange = function() { // Ждём ответа от сервера
      if (xmlhttp.readyState == 4) { // Ответ пришёл
        if(xmlhttp.status == 200) { // Сервер вернул код 200 (что хорошо)
          if(xmlhttp.responseText == '1'){ // Выводим ответ сервера
			document.getElementById("line" + id_tovar).innerHTML = '<td></td><td></td><td></td>';
		  }else{
			alert('Ошибка при удалении товара. ' + xmlhttp.responseText);
		  }
        }
      }
    };
  }
</script>