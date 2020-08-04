{% extends 'base.html.twig' %}

{% block title %}List!{% endblock %}

{% block body %}
<div class="<?= $entity['shortNamePlural']; ?>-wrapper">

    {% for <?= $entity['shortNameToLower']; ?> in <?= $entity['shortNamePlural']; ?> %}
    <div class="<?= $entity['shortNameToLower']; ?>-wrapper">
<?php foreach ($entity['properties'] as $property) : ?>
<?php if (!in_array($property->getName(), ['id', 'uuid'])) : ?>
        {% trans from '<?= $entity['shortNameToLower']; ?>' %}<?= $property->getName(); ?>{% endtrans %}: {{ <?= $entity['shortNameToLower']; ?>.<?= $property->getName(); ?> }}<br/>
<?php endif; ?>
<?php endforeach; ?>

        <a href="{{ path('<?= $entity['shortNameToLower']; ?>.detail', {'uuid': <?= $entity['shortNameToLower']; ?>.uuid}) }}">{% trans from 'global' %}Detail{% endtrans %}</a>
    </div>
    {% endfor %}

</div>
<a href="{{ path('<?= $entity['shortNameToLower']; ?>.create') }}">{% trans from 'global' %}Create{% endtrans %}</a>
{% endblock %}
