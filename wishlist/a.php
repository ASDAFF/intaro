<?php
/*

	Обработка ajax запросов

*/
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("wishlist");
$APPLICATION->RestartBuffer();
		
	// Если запрос на удаление товара из Wishlist'а
	if((int)$_POST['del'] === 1){
		$id_tovar = ((int)$_POST['id_tovar']) * 1;
		$id_user  = ((int)$_POST['id_user']) * 1;
		if($id_user == ($USER -> GetID())){
			// Если товар есть в Wishlist'е пользователя
			if(Wishlist::ExistTovar($id_tovar, $id_user)){
				// удаляем
				if(Wishlist::DelTovar($id_tovar, $id_user)){
					$r =  '1';
				}else{
					$r = 'NO';
				}
			}else{
				$r = 'Товара нет в Wishlist\'е';
			}
		}else{
			$r = 'Ошибка id пользователя';
		}
	}else{
		// Запрос на добавление това в Wishlist
		$id_tovar = ((int)$_POST['id']) * 1;
		$id_user  = (int)$USER -> GetID() * 1;
		
		// Если товара еще нет в Wishlist'е
		if(! Wishlist::ExistTovar($id_tovar, $id_user)){
			if(Wishlist::Insert($id_tovar, $id_user)){
				$r =  '1';
			}else{
				$r = 'NO';
			}
		}else{
			$r = 'Товар уже находится в вашем Wishlist\'е';
		}
	}
	$APPLICATION->RestartBuffer();
	echo $r;