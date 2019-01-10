<?php
defined('ROOT') OR exit('No direct script access allowed');

$action = (isset($_GET['action'])) ? $_GET['action'] : '';
$msg = (isset($_GET['msg'])) ? urldecode($_GET['msg']) : '';
$msgType = (isset($_GET['msgType'])) ? $_GET['msgType'] : '';
$newsManager = new newsManager();

switch($action){
	case 'saveconf':
		if($administrator->isAuthorized()){
			$runPlugin->setConfigVal('label', trim($_REQUEST['label']));
			$runPlugin->setConfigVal('itemsByPage', trim(intval($_REQUEST['itemsByPage'])));
			$runPlugin->setConfigVal('hideContent', (isset($_POST['hideContent']) ? 1 : 0));
			$runPlugin->setConfigVal('comments', (isset($_POST['comments']) ? 1 : 0));
			$pluginsManager->savePluginConfig($runPlugin);
			header('location:index.php?p=news');
			die();
		}
		break;
	case 'save':
		if($administrator->isAuthorized()){
			$imgId = '';
			if($pluginsManager->isActivePlugin('galerie')){
				$galerie = new galerie();
				$img = ($_REQUEST['imgId']) ? $galerie->createItem($_REQUEST['imgId']) : new galerieItem();
				if($img){
					$img->setCategory('');
					$img->setTitle($_POST['name'].' (image à la une)');
					$img->setContent('');
					$img->setDate(date('Y-m-d H:i:s'));
					$img->setHidden(1);
					$galerie->saveItem($img);
					$imgId = $galerie->getLastId().'.jpg';
				}
			}
			$news = ($_REQUEST['id']) ?  $newsManager->create($_REQUEST['id']) : new news();
			$news->setName($_REQUEST['name']);
			$news->setContent($_REQUEST['content']);
			$news->setDraft((isset($_POST['draft']) ? 1 : 0));
			if($_REQUEST['date'] == "") $news->setDate($news->getDate());
			else $news->setDate($_REQUEST['date']);
			$news->setImg($imgId);
			if($newsManager->saveNews($news)){
				$msg = "Les modifications ont été enregistrées";
				$msgType = 'success';
			}
			else{
				$msg = "Une erreur est survenue";
				$msgType = 'error';
			}
			header('location:index.php?p=news&msg='.urlencode($msg).'&msgType='.$msgType);
			die();
		}
		break;
	case 'edit':
		$mode = 'edit';
		$news = (isset($_REQUEST['id'])) ?  $newsManager->create($_GET['id']) : new news();
		$news = array(
			'id' => $news->getId(),
			'name' => $news->getName(),
			'content' => $news->getContent(),
			'date' => $news->getDate(),
			'draft' => $news->getDraft(),
			'img' => $news->getImg(),
		);
		$showDate = (isset($_REQUEST['id'])) ?  true : false;
		break;
	case 'del':
		if($administrator->isAuthorized()){
			$news = $newsManager->create($_REQUEST['id']);
			if($newsManager->delNews($news)){
				$msg = "Les modifications ont été enregistrées";
				$msgType = 'success';
			}
			else{
				$msg = "Une erreur est survenue";
				$msgType = 'error';
			}
			header('location:index.php?p=news&msg='.urlencode($msg).'&msgType='.$msgType);
			die();
		}
		break;
	case 'listcomments':
		$mode = 'listcomments';
		$newsManager->loadComments($_GET['id']);
		break;
	case 'delcomment':
		if($administrator->isAuthorized()){
			$newsManager->loadComments($_GET['id']);
			$comment = $newsManager->createComment($_GET['idcomment']);
			if($newsManager->delComment($comment)){
				$msg = "Les modifications ont été enregistrées";
				$msgType = 'success';
			}
			else{
				$msg = "Une erreur est survenue";
				$msgType = 'error';
			}
			header('location:index.php?p=news&action=listcomments&id='.$_GET['id'].'&msg='.urlencode($msg).'&msgType='.$msgType);
			die();
		}
		break;
	case 'updatecomment':
		if($administrator->isAuthorized()){
			$newsManager->loadComments($_GET['id']);
			$comment = $newsManager->createComment($_GET['idcomment']);
			$newsManager->delComment($comment);
			$comment->setContent($_POST['content'.$_GET['idcomment']]);
			if($newsManager->saveComment($comment)){
				$msg = "Les modifications ont été enregistrées";
				$msgType = 'success';
			}
			else{
				$msg = "Une erreur est survenue";
				$msgType = 'error';
			}
			header('location:index.php?p=news&action=listcomments&id='.$_GET['id'].'&msg='.urlencode($msg).'&msgType='.$msgType);
			die();
		}
		break;
	default:
		$mode = 'list';
}
?>