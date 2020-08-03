{% extends 'base.html.twig' %}

{% block title %}Type!{% endblock %}

{% block body %}
<div class="form-wrapper">
    {{ form_start(form) }}

<?php foreach ($entity['properties'] as $property) : ?>
<?php if (!in_array($property->getName(), ['id', 'uuid'])) : ?>
    <div>
        {% if form.<?= $property->getName(); ?>.vars.errors|length %}
        <div class="form-error-wrapper">
            {{ form_errors(form.<?= $property->getName(); ?>) }}
        </div>
        {% endif %}
        {{ form_label(form.<?= $property->getName(); ?>) }}
        {{ form_widget(form.<?= $property->getName(); ?>) }}
    </div>
<?php endif; ?>
<?php endforeach; ?>

    <div>
        {{ form_widget(form.save) }}
    </div>

    {{ form_end(form) }}
</div>
{% endblock %}
