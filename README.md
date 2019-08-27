# VirtualInventory

チェスト等がない場所でもインベントリを開けるようになります. 
APIのみの提供となるので, 各自連携用のプラグインを用意してください.

Feel free to contribution.


## 使い方
 
使い方は`uramnoil\virtualinventory\VirtualInventoryAPI` インターフェースに記述しています.

`Server::getInstance()->getPluginManager()->getPlugin('VirtualInventory')->getAPI()`でAPIを取得できます.


## 将来追加する予定の機能

別プラグインで実装します.

* フォームを用いたインベントリの管理.
* 複数プレイヤー間でのリアルタイムの共有.
* 複数のインベントリ間でのアイテムの移動の簡易化.
* 経済システムとの連携.
* フレンドシステムとの連携.

経済システムとフレンドシステムに関しては既存のプラグインと連携する気がないので, 使いやすいまたは自分で作ったプラグインができるまでは実装するつもりはありません.


## LICENSE

[MIT License](https://opensource.org/licenses/mit-license.php)