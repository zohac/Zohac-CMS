{% extends 'base.html.twig' %}

{% block title %}Detail!{% endblock %}

{% block body %}
<div class="<?= $entity['shortNameToLower']; ?>-wrapper">

    {% if <?= $entity['shortNameToLower']; ?> is not empty %}
<?php foreach ($entity['properties'] as $property) : ?>
<?php if ('id' != $property->getName()) : ?>
    {% trans from '<?= $entity['shortNameToLower']; ?>' %}<?= $property->getName(); ?>{% endtrans %}: {{ <?= $entity['shortNameToLower']; ?>.name }}<br/>
<?php endif; ?>
<?php endforeach; ?>

    <a href="{{ path('<?= $entity['shortNameToLower']; ?>.list') }}">{% trans from 'global' %}List{% endtrans %}</a>
    <a href="{{ path('<?= $entity['shortNameToLower']; ?>.update', {"uuid": <?= $entity['shortNameToLower']; ?>.uuid}) }}">{% trans from 'global' %}Update{% endtrans %}</a>
    <a href="{{ path('<?= $entity['shortNameToLower']; ?>.delete', {"uuid": <?= $entity['shortNameToLower']; ?>.uuid}) }}">{% trans from 'global' %}Delete{% endtrans %}</a>
    {% endif %}

</div>

{% endblock %}
