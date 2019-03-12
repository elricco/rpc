<?php

/** @var rex_yform_choice_list $choiceList */
/** @var rex_yform_choice_list_view $choiceListView */

$notices = [];
if ($this->getElement('notice')) {
    $notices[] = rex_i18n::translate($this->getElement('notice'), false);
}
if (isset($this->params['warning_messages'][$this->getId()]) && !$this->params['hide_field_warning_messages']) {
    $notices[] = '<span class="text-warning">'.rex_i18n::translate($this->params['warning_messages'][$this->getId()], false).'</span>';
}

if (!isset($groupAttributes)) {
    $groupAttributes = [];
}

$groupClass = 'form-group';
if (isset($groupAttributes['class']) && is_array($groupAttributes['class'])) {
    $groupAttributes['class'][] = $groupClass;
} elseif (isset($groupAttributes['class'])) {
    $groupAttributes['class'] .= ' '.$groupClass;
} else {
    $groupAttributes['class'] = $groupClass;
}

if (!isset($elementAttributes)) {
    $elementAttributes = [];
}
$elementClass = trim(($choiceList->isMultiple() ? 'checkbox form-check' : 'radio form-check form-check-inline').' '.$this->getWarningClass());
if (isset($elementAttributes['class']) && is_array($elementAttributes['class'])) {
    $elementAttributes['class'][] = $elementClass;
} elseif (isset($elementAttributes['class'])) {
    $elementAttributes['class'] .= ' '.$elementClass;
} else {
    $elementAttributes['class'] = $elementClass;
}

?>

<?php $choiceOutput = function (rex_yform_choice_view $view) use ($elementAttributes) {
    ?>
    <div<?= rex_string::buildAttributes($elementAttributes) ?>>
        <input class="form-check-input"
            value="<?= rex_escape($view->getValue()) ?>"
            <?= (in_array($view->getValue(), $this->getValue(), true) ? ' checked="checked"' : '') ?>
            <?= $view->getAttributesAsString() ?>
        />
        <?php $for_id = $view->getAttributes() ?>
        <label class="form-check-label"
        for="<?= $for_id['id'] ?>"
        >
            <i class="form-helper"></i>
            <?= rex_escape($view->getLabel()) ?>
        </label>
    </div>
<?php
} ?>

<?php $choiceGroupOutput = function (rex_yform_choice_group_view $view) use ($choiceOutput) {
        ?>
    <div class="form-group row">
        <div class="col-sm-2"><?= rex_escape($view->getLabel()) ?></div>
        <div class="col-sm-10">
            <?php foreach ($view->getChoices() as $choiceView): ?>
                <?php $choiceOutput($choiceView) ?>
            <?php endforeach ?>
        </div>
    </div>
<?php
    } ?>

<div<?= rex_string::buildAttributes($groupAttributes) ?>>
    <?php if ($this->getLabel()): ?>
        <div>
            <?= rex_escape($this->getLabelStyle($this->getLabel())) ?>
        </div>
    <?php endif ?>

    <?php foreach ($choiceListView->getPreferredChoices() as $view): ?>
        <?php $view instanceof rex_yform_choice_group_view ? $choiceGroupOutput($view) : $choiceOutput($view) ?>
    <?php endforeach ?>

    <?php foreach ($choiceListView->getChoices() as $view): ?>
        <?php $view instanceof rex_yform_choice_group_view ? $choiceGroupOutput($view) : $choiceOutput($view) ?>
    <?php endforeach ?>

    <?php if ($notices): ?>
        <p class="help-block"><?= implode('<br />', $notices) ?></p>
    <?php endif ?>
</div>
