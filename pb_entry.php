<?php   
    namespace Module;

    use \Library\ModuleConfig;
    use \Library\VirtualPath;
    use \Library\Controller;
    use \Library\Objects;
    use \Library\Assets;
    use \Helper\Header;
    use \Helper\Request;
    use \Helper\ApiResponse as Respond;

    class LinkShortener {
        public function requestHandler($params) {
            $controller = new Controller;
            $userModel = $controller->__model('user');
            if (!isset($params[0])) {
                die("No action requested.");
            } else {
                switch($params[0]) {
                    case 'link':
                        if (isset($params[1])) {
                            $obj = new Objects;
                            $url = $obj->get('mod_link-shortener_link', $params[1], 'url');
                            if ($url) {
                                Header::Location($url);
                            } else {
                                die('unknown link');
                            }
                        } else {
                            die('missing link');
                        }

                        break;
                    case 'create-link':
                        if (!Request::requireAuthentication()) die();
                        if ($userModel->check('module.link-shortener.create-link')) {
                            $postdata = Request::parseBody();
                            if (!isset($postdata->link) || !isset($postdata->target)) {
                                Respond::error('missing_information', 'either the link or target is missing from your request');
                            } else {
                                if (substr($postdata->link, 0, 1) == '/') $postdata->link = substr($postdata->link, 1);
                                if (substr($postdata->link, -1) == '/') $postdata->link = substr($postdata->link, 0, -1);

                                $obj = new Objects;
                                $obj->create('mod_link-shortener_link', $postdata->link);
                                $obj->set('mod_link-shortener_link', $postdata->link, 'link', $postdata->link);
                                $obj->set('mod_link-shortener_link', $postdata->link, 'url', $postdata->target);

                                $vPath = new VirtualPath;
                                $vPath->create($postdata->link, 'pb-loader/module/link-shortener/link/' . $postdata->link, 'en');
                                Respond::success();
                            }
                        } else {
                            Respond::error("missing_privileges", "You are lacking the permission to create a new link.");
                        }

                        break;
                    case 'delete-link':
                        if (!Request::requireAuthentication()) die();
                        if ($userModel->check('module.link-shortener.delete-link')) {
                            $link = $params[1];
                            if (substr($link, 0, 1) == '/') $link = substr($link, 1);
                            if (substr($link, -1) == '/') $link = substr($link, 0, -1);

                            $obj = new Objects;
                            $obj->purge('mod_link-shortener_link', $link);

                            $vPath = new VirtualPath;
                            $vPath->delete($link, 'pb-loader/module/link-shortener/link/' . $link, 'en');
                            Respond::success();
                        } else {
                            Respond::error("missing_privileges", "You are lacking the permission to delete a link.");
                        }

                        break;
                    case 'list-links':
                        if (!Request::requireAuthentication()) die();
                        if ($userModel->check('module.link-shortener.list-links')) {
                            $obj = new Objects;
                            $list = $obj->list('mod_link-shortener_link');
                            $final = array();

                            foreach($list as $item) {
                                $link = $item['name'];
                                $target = $obj->get('mod_link-shortener_link', $link, 'url');
                                $final[$link] = $target;
                            }

                            Respond::success(array(
                                "list" => $final
                            ));
                        } else {
                            Respond::error("missing_privileges", "You are lacking the permission to list links.");
                        }

                        break;
                    default:
                        die('Unknown action requested.');
                        break;
                }
            }
        }

        public function configurator() {
            $assets = new Assets;
            $assets->registerHead('style', "configurator.css", array("origin" => "module:link-shortener", "permanent" => true));
            $assets->registerBody('script', "configurator.js", array("origin" => "module:link-shortener", "permanent" => true));
            require DYNAMIC_DIR . '/modules/link-shortener/configurator.php';
        }
    }