<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Tools\TransfertLanguage\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Tools\TransfertLanguage\TransfertLanguage;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public $app;

    protected function init()
    {
      $CLICSHOPPING_TransfertLanguage = new TransfertLanguage();
      Registry::set('TransfertLanguage', $CLICSHOPPING_TransfertLanguage);

      $this->app = Registry::get('TransfertLanguage');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
