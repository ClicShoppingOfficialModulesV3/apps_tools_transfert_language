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


  namespace ClicShopping\Apps\Tools\TransfertLanguage\Sites\ClicShoppingAdmin\Pages\Home\Actions\TransfertLanguage;

  use ClicShopping\OM\Cache;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class UpgadeFromLanguagePack extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;
    protected $transfert_directory;

    public function __construct()
    {
      $this->app = Registry::get('TransfertLanguage');
      $this->transfert_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/ClicShopping/Work/LanguagePackages/';
    }

    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      $tmp_file = CLICSHOPPING::BASE_DIR . 'Work/Temp/language.tmp';

      if (is_file($tmp_file)) {
        $deleted = 0;
        $modified = 0;
        $added = 0;

        foreach (file($tmp_file) as $line) {

          $sql = json_decode($line, true);
          if (isset($sql['save']['data'])) {

            if (isset($sql['save']['where'])) {
              $this->app->db->save($sql['save']['table'], $sql['save']['data'], $sql['save']['where']);
              $modified++;
            } else {
              $this->app->db > save($sql['save']['table'], $sql['save']['data']);
              $added++;
            }
          } elseif (isset($sql['delete'])) {
            $this->app->db->delete($sql['delete']['table'], $sql['delete']['where']);
            $deleted++;
          }
        }

        unlink($tmp_file);

        Cache::clear('languages');

        $CLICSHOPPING_MessageStack->add($this->app->getDef('success_define_language_table_upgrade', ['deleted' => $deleted, 'added' => $added, 'modified' => $modified, 'all_data' => ($deleted + $added + $modified)]), 'success');
      }

      $this->app->redirect('TransfertLanguage');
    }
  }