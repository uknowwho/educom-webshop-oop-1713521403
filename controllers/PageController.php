<?php
require_once("models/PageModel.php");
require_once("AJAXController.php");
class PageController {
    private $model;
    private $modelFactory;
    public function __construct($modelFactory) {
        $this->modelFactory = $modelFactory;
        $this->model = $this->modelFactory->createModel('page');
    }

    public function handleRequest() {
        $this->getRequest();
        $this->processRequest();
        $this->showResponse();
    }

    private function getRequest() {
        $this->model->getRequestedPage();
    }

    private function processRequest() {
        if ($this->model->action=="ajax") {
            $ajax = new AJAXController($this->model, $this->modelFactory);
            $ajax->handleRequest();
            // is dit gebruikelijk?
            exit;
        }

        switch ($this->model->page) {
            case "login":
                require_once("models/UserModel.php");
                $this->model = $this->modelFactory->createModel("user");
                $this->model->validateLogin();
                if ($this->model->valid) {
                    $this->model->doLoginUser();
                    $this->model->page = "home";
                }
                break;

            case "logout":
                require_once("models/UserModel.php");
                $this->model = $this->modelFactory->createModel("user");
                $this->model->doLogoutUser();
                $this->model->page = "home";
                break;

            case "register":
                require_once("models/UserModel.php");
                $this->model = $this->modelFactory->createModel("user");
                $this->model->validateRegister();
                if ($this->model->valid) {
                    $this->model->addAccount();
                    $this->model->page = "home";
                }
                break;

            case "account":
                require_once("models/UserModel.php");
                $this->model = $this->modelFactory->createModel("user");
                $this->model->validateChangePassword();
                if ($this->model->valid) {
                    $this->model->changePassword();
                    $this->model->page = "home";
                }
                break;

             case "contact":
                require_once("models/UserModel.php");
                $this->model = $this->modelFactory->createModel("user");
                $this->model->validateContact();
                if ($this->model->valid) {
                    $this->model->page = "thanks";
                }
                break;
                
            case "shop":
                require_once("models/ProductModel.php");
                $this->model = $this->modelFactory->createModel("product");
                $this->model->getProducts();
                $this->model->handleCartAction();
                break;

            case "cart":
                require_once("models/ProductModel.php");
                $this->model = $this->modelFactory->createModel("product");
                $this->model->getCartProducts();
                $this->model->handleCartAction();
                break;    

            case "detail":
                require_once("models/ProductModel.php");
                $this->model = $this->modelFactory->createModel("product");
                $this->model->getDetailProduct();
                $this->model->handleCartAction();
                break;
            
            case "topK":
                require_once("models/ProductModel.php");
                $this->model = $this->modelFactory->createModel("product");
                $this->model->getTopKProducts();
                $this->model->handleCartAction();
                break;

        }
    }

    private function showResponse() {
        $this->model->createMenu();
        $this->model->setTitle();
        $view = NULL;

        switch ($this->model->page) {
            case "home":
                include_once("views/HomeDoc.php");
                $view = new HomeDoc($this->model);
                break;

            case "about":
                include_once("views/AboutDoc.php");
                $view = new AboutDoc($this->model);
                break;

            case "login":
                include_once("views/LoginDoc.php");
                $view = new LoginDoc($this->model);
                break;    

            case "register":
                include_once("views/RegisterDoc.php");
                $view = new RegisterDoc($this->model);
                break;

            case "account":
                include_once("views/AccountDoc.php");
                $view = new AccountDoc($this->model);
                break;

            case "contact":
                include_once("views/ContactDoc.php");
                $view = new ContactDoc($this->model);
                break;

            case "thanks":
                include_once("views/ThanksDoc.php");
                $view = new ThanksDoc($this->model);
                break;

            case "shop":
                include_once("views/ShopDoc.php");
                $view = new ShopDoc($this->model);
                break;

            case "cart":
                include_once("views/CartDoc.php");
                $view = new CartDoc($this->model);
                break;

            case "detail":
                include_once("views/DetailDoc.php");
                $view = new DetailDoc($this->model);
                break;

            case "topK":
                include_once("views/TopKDoc.php");
                $view = new TopKDoc($this->model);
                break;

            default:
                include_once("views/Error404Doc.php");
                $view = new Error404Doc($this->model);
                break;
        }
        $view->show();
    }
}