<?php

/**
 * @copyright (C) 2022, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Jonathan Coulet <j.coulet@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Frédéric Kaplon <frederic.kaplon@me.com>
 * @author Florent Fortat <florent.fortat@maxgun.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

require_once PLUGINS . 'blog/entities/news.php';
require_once PLUGINS . 'blog/entities/newsComment.php';
require_once PLUGINS . 'blog/entities/newsManager.php';
require_once PLUGINS . 'blog/entities/BlogCategoriesManager.php';
require_once PLUGINS . 'blog/entities/BlogCategory.php';



## Fonction d'installation

function blogInstall() {

}

## Hooks

function blogEndFrontHead() {
    $core = core::getInstance();
    echo '<link rel="alternate" type="application/rss+xml" href="' . router::getInstance()->generate('blog-rss') . '" title="' . $core->getConfigVal('siteName') . '">' . "\n";
}

function BlogAdminCategoriesTemplates() {
    global $runPlugin;
    if ($runPlugin->getName() !== 'blog') {
        return;
    }

    $catsManager = new BlogCategoriesManager();
    echo '<section id="categories_panel">';
    echo '<header>Gestion des catégories du blog</header>';
    echo $catsManager->outputAsList();
    echo '</section>';

    echo '<a title="Gérer les catégories" id="cat_link" data-fancybox href="#" data-src="#categories_panel"'
        . '"><i class="fa-regular fa-folder-open"></i></a>';
}


## Code relatif au plugin

