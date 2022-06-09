<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* config/form_display/errors.twig */
class __TwigTemplate_055968dcef9c3692f8b9f2f6ce251d44 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<dl>
    <dt>";
        // line 2
        echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
        echo "</dt>
    ";
        // line 3
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["error_list"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["error"]) {
            // line 4
            echo "        <dd>";
            echo $context["error"];
            echo "</dd>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['error'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 6
        echo "</dl>
";
    }

    public function getTemplateName()
    {
        return "config/form_display/errors.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  57 => 6,  48 => 4,  44 => 3,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "config/form_display/errors.twig", "/srv/users/api-development/apps/api-development/public/phpMyAdmin/templates/config/form_display/errors.twig");
    }
}
