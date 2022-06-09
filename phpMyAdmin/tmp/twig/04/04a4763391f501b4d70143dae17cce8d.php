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

/* setup/error.twig */
class __TwigTemplate_d28a76517d316732647a70f6ae5ae29c extends Template
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
        echo "<div class=\"error\">
  <h4>";
echo _gettext("Warning");
        // line 2
        echo "</h4>
  <p>";
echo _gettext("Submitted form contains errors");
        // line 3
        echo "</p>
  <p>
    <a href=\"";
        // line 5
        echo PhpMyAdmin\Url::getCommon(twig_array_merge(($context["url_params"] ?? null), ["mode" => "revert"]));
        echo "\">
      ";
echo _gettext("Try to revert erroneous fields to their default values");
        // line 7
        echo "    </a>
  </p>
</div>

";
        // line 11
        echo ($context["errors"] ?? null);
        echo "

<a class=\"btn\" href=\"index.php";
        // line 13
        echo PhpMyAdmin\Url::getCommon();
        echo "\">
  ";
echo _gettext("Ignore errors");
        // line 15
        echo "</a>

<a class=\"btn\" href=\"";
        // line 17
        echo PhpMyAdmin\Url::getCommon(twig_array_merge(($context["url_params"] ?? null), ["mode" => "edit"]));
        echo "\">
  ";
echo _gettext("Show form");
        // line 19
        echo "</a>
";
    }

    public function getTemplateName()
    {
        return "setup/error.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  79 => 19,  74 => 17,  70 => 15,  65 => 13,  60 => 11,  54 => 7,  49 => 5,  45 => 3,  41 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "setup/error.twig", "/srv/users/api-development/apps/api-development/public/phpMyAdmin/templates/setup/error.twig");
    }
}
