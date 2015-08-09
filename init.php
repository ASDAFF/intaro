<?
/*
	Класс по работе с разделом Wishlist
	я его разместил в 
	\Bitrix\www\bitrix\php_interface\init.php
*/
class Wishlist{
	// Добавление нового товара в Wishlist пользователя
	function Insert($id_tovar, $id_user){
		global $DB;
		
		// поля таблицы
		$arFields = array(
			"id_user"  => $id_user,
			"id_tovar" => $id_tovar
		);

		// начинаем транзакцию
		$DB->StartTransaction();
		$id = $DB->Insert("wishlist", $arFields, $err_mess);
		
		// Проверяем на наличее ошибок
		if(strlen($strError) <= 0){
			$DB->Commit();
			return true;
		}else{
			$DB->Rollback();
			return false;
		}
	}
	// Проверяем наличие товара в Wishlist'е пользователя
	function ExistTovar($id_tovar, $id_user){
		global $DB;
		
		$sql = "SELECT id FROM wishlist WHERE (id_user = $id_user) and (id_tovar = $id_tovar) LIMIT 1;";
		
		$res = $DB->Query($sql, false, $err_mess);
		$res = $res->SelectedRowsCount();// количество выбранных записей
		
		if(!empty($res)) return true;
		else return false;
	}
	// Удаляем товар из Wishlist'а пользователя
	function DelTovar($id_tovar, $id_user){
		global $DB;
		
		$sql = "DELETE FROM wishlist WHERE (id_user = $id_user) && (id_tovar = $id_tovar);";
		
		$res = $DB->Query($sql, false, $err_mess);
		$res = $res->AffectedRowsCount();// количество измененных записей

		if(!empty($res)) return true;
		else return false;
	}
	// Выбрать товары из Wishlist'а пользователя
	function GetWishlist($id_user, $countpost=5, $page=1){
		$shift = $countpost * ($page - 1);
		global $DB;
		
		$sql = "SELECT id_tovar FROM wishlist WHERE (id_user = $id_user) ORDER BY id DESC LIMIT $shift, $countpost;";
		
		$res = $DB->Query($sql, false, $err_mess);
		
		
		 while($arGroup = $res->GetNext()){
			$result[] = $arGroup;
		}
		
		return $result;// Массив элементов
	}
	// Количество товаров в Wishlist'е пользователя
	function CountWishlist($id_user){
		global $DB;
		$sql = "SELECT count(id) AS count FROM wishlist WHERE (id_user = $id_user);";
		$res = $DB->Query($sql, false, $err_mess);
		$arGroup = $res->GetNext();
		return (int)$arGroup['count'];
	}
	// Выводим 3 популярных товара
	function getPopularTovar(){
		// Проверим есть ли сохраненные данные и если нет то сделаем запрос к базе данных
		$file = './wishlist/cache.txt';
		$arCache = unserialize(file_get_contents($file));

		// Если прошло меньше 5 минут с момента последнего обновления данных
		if( ((time() - $arCache['data']) < 300) ){
			$html = $arCache['html'];
		}else{
			global $DB;
			$sql = "SELECT id_tovar FROM wishlist GROUP BY id_tovar ORDER BY COUNT(*) DESC LIMIT 3;";
			$res = $DB->Query($sql, false, $err_mess);
			 while($arGroup = $res->GetNext()){
				$result[] = $arGroup;
			}
			
			// Сформируем html код для вывода в браузер
			$html = '<div>';
			$html .= '<table width="100%">';
			foreach($result as $item){
				// Получаем данные о товаре из базы по его id
				$res = CIBlockElement::GetList(array(), array('ID'=>$item['id_tovar']), false, false, array('ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL'));
				// заносим в массив полученные данные
				$arElement = $res->GetNext();

				$arFilter = Array("IBLOCK_ID"=>$arElement[IBLOCK_ID], "ID"=>$arElement[ID]);
				$ress = CIBlockElement::GetList(Array(), $arFilter);
				if ($ob = $ress->GetNextElement()){ 
					$arFields = $ob->GetFields();
				}
				
				$html .= '<tr>';
					// Картинка
					$html .= '<td>';
						if(!empty($arFields['PREVIEW_PICTURE'])){
							$html .=  CFile::ShowImage($arFields['PREVIEW_PICTURE']); 
						}else{
							$html .=  '<img src="/bitrix/components/bitrix/catalog/templates/mydefault/bitrix/catalog.element/.default/images/no_photo.png" width="68" />';
						}
					$html .= '</td>';
					// Ссылка
					$html .= '<td style="padding:10; font-size:16px;" width="100%">';
					$html .=  "<a href='". $arElement['DETAIL_PAGE_URL'] ."'>". $arElement['NAME'] ."</a>";
					$html .= '</td>';
				$html .= '</tr>';
			}
			$html .= '</table>';
			$html .= '</div>';
			
			// Сохраним html в файл
			$current = serialize(array(
				'data' => time(),
				'html' => $html
			));
			return var_dump(file_put_contents($file, $current));
		}
		return $html;
	}
}



?>