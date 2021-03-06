
Soft Delete
===========
Реализация SoftDelete для моделей ActiveRecord в Yii2.



## Зачем это нужно

Суть работы **SoftDelete** (мягкого удаления) - вместо окончательного удаления записей из БД, в них просто меняется флаг (например, deleted=1). При выборке из БД мы опять же по этому флагу "отсеиваем" все удалённые записи, и получаем только актуальные.

Зачем это нужно, не проще ли сразу удалять навсегда?

В более-менее сложных приложениях, есть статистика, отчёты и связанные данные. Например, покупатели делают заказы на сайте. Допустим, мы удалили покупателя, а вместе с ним и все его заказы. В результате мы не можем достоверно сказать, сколько у нас было заказов и на какую сумму за определённый период. В таких случаях помогает SoftDelete. По умолчанию мы игнорируем записи, отмеченные как удалённые, но при необходимости можем их учитывать и брать из них любую информацию.

Также, сохранение удалённых записей теоретически может пригодиться при отладке кода и восстановлении случайно удалённых данных. Но на практике это почти никогда не случается. Тем более, что для восстановления гораздо удобнее использовать регулярные бекапы. Поэтому, решив использовать SoftDelete, взвесьте все за и против. Возможно, в вашем проекте он не принесёт пользы, а будет только мешать.



Установка
------------

Лучше всего устанавливать через [Composer](http://getcomposer.org/download/).

Выполните

```
php composer.phar require --prefer-dist nex-otaku/yii2-softdelete "*"
```

или же добавьте

```
"nex-otaku/yii2-softdelete": "*"
```

в список "require" вашего файла `composer.json`.



Схема БД.
------------

В таблицах, добавляем поле "deleted": ```TINYINT(1) NOT NULL DEFAULT '1'```.
Для всех внешних ключей таблицы устанавливаем режим ```ON DELETE RESTRICT```. Это частично защитит нас от случайного "жёсткого" удаления записей.



Подключение расширений.
------------

Расширения подгружаются автоматически, через зависимости композера.

Используются расширения:

1. SoftDeleteBehavior.
   https://github.com/yii2tech/ar-softdelete

2. QueryScopeBehavior для отображения всех записей, кроме удалённых.
   https://github.com/mdmsoft/yii2-ar-behaviors

3. NotDeletedTrait, SoftDeleteBehavior (сконфигурированный под поле "deleted"), SoftDeleteTimestampBehavior, SoftDeleteBlameableBehavior.
   https://github.com/nex-otaku/yii2-softdelete

Бихевиор QueryScopeBehavior подключается к ActiveQuery автоматически. Его нигде не нужно специально прописывать. Установили расширение и он уже сам в бутстрапе прилепился к ActiveQuery.



Подключение в модели.
------------

В модели подключаем трейт **NotDeletedTrait** и бихевиор **SoftDeleteBehavior**. Если нам нужно знать, кто и когда удалил запись, подключаем **SoftDeleteTimestampBehavior** и **SoftDeleteBlameableBehavior**. Не забываем добавить соответствующие поля в таблице БД - "deleted_by", "deleted_at".

Так как эти поля управляются автоматически и недоступны для редактирования пользователем напрямую, удаляем их из правил модели. См. метод "rules()".

Пример.

```
class Feed extends \yii\db\ActiveRecord
{
    use \nex_otaku\softdelete\NotDeletedTrait;
    public function behaviors()
    {
        return [
            SoftDeleteBehavior::className(),
            SoftDeleteTimestampBehavior::className(),
            SoftDeleteBlameableBehavior::className(),
        ];
    }
}
```



Использование
-----

```
// Удаление.
$model->safeDelete();

// Выборка записей, которые не были удалены.
$coolModels = CoolModel::find()->notDeleted()->all();
```

Каскадное удаление реализуется самостоятельно.