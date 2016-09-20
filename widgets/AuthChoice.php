<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace xutl\authclient\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\authclient\ClientInterface;

/**
 * AuthChoice prints buttons for authentication via various auth clients.
 * It opens a popup window for the client authentication process.
 * By default this widget relies on presence of [[\yii\authclient\Collection]] among application components
 * to get auth clients information.
 *
 * Example:
 *
 * ```php
 * <?= yii\authclient\widgets\AuthChoice::widget([
 *     'baseAuthUrl' => ['site/auth']
 * ]); ?>
 * ```
 *
 * You can customize the widget appearance by using [[begin()]] and [[end()]] syntax
 * along with using method [[clientLink()]] or [[createClientUrl()]].
 * For example:
 *
 * ```php
 * <?php
 * use yii\authclient\widgets\AuthChoice;
 * ?>
 * <?php $authAuthChoice = AuthChoice::begin([
 *     'baseAuthUrl' => ['site/auth']
 * ]); ?>
 * <ul>
 * <?php foreach ($authAuthChoice->getClients() as $client): ?>
 *     <li><?= $authAuthChoice->clientLink($client) ?></li>
 * <?php endforeach; ?>
 * </ul>
 * <?php AuthChoice::end(); ?>
 * ```
 *
 * This widget supports following keys for [[ClientInterface::getViewOptions()]] result:
 *  - popupWidth - integer width of the popup window in pixels.
 *  - popupHeight - integer height of the popup window in pixels.
 *  - widget - configuration for the widget, which should be used to render a client link;
 *    such widget should be a subclass of [[AuthChoiceItem]].
 *
 * @see \yii\authclient\AuthAction
 *
 * @property array $baseAuthUrl Base auth URL configuration. This property is read-only.
 * @property ClientInterface[] $clients Auth providers. This property is read-only.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
class AuthChoice extends \yii\authclient\widgets\AuthChoice
{
    /**
     * Initializes the widget.
     */
    public function init()
    {
        $view = Yii::$app->getView();
        if ($this->popupMode) {
            AuthChoiceAsset::register($view);
            if (empty($this->clientOptions)) {
                $options = '';
            } else {
                $options = Json::htmlEncode($this->clientOptions);
            }
            $view->registerJs("\$('#" . $this->getId() . "').authchoice({$options});");
        } else {
            AuthChoiceStyleAsset::register($view);
        }
        $this->options['id'] = $this->getId();
        echo Html::beginTag('div', $this->options);
    }
}