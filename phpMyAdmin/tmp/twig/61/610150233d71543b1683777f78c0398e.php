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

/* setup/base.twig */
class __TwigTemplate_58054a09cac5de9db47aac9c0e3a6172 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<!doctype html>
<html>
<head>
  <meta charset=\"utf-8\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
  <title>phpMyAdmin setup</title>
  <link href=\"../favicon.ico\" rel=\"icon\" type=\"image/x-icon\">
  <link href=\"../favicon.ico\" rel=\"shortcut icon\" type=\"image/x-icon\">
  <link href=\"../setup/styles.css\" rel=\"stylesheet\" type=\"text/css\">
  <script type=\"text/javascript\" src=\"../js/vendor/jquery/jquery.min.js\"></script>
  <script type=\"text/javascript\" src=\"../js/vendor/jquery/jquery-ui.min.js\"></script>
  <script type=\"text/javascript\" src=\"../js/vendor/bootstrap/bootstrap.bundle.min.js\"></script>
  <script type=\"text/javascript\" src=\"../js/dist/setup/ajax.js\"></script>
  <script type=\"text/javascript\" src=\"../js/dist/config.js\"></script>
  <script type=\"text/javascript\" src=\"../js/dist/setup/scripts.js\"></script>
  <script type=\"text/javascript\" src=\"../js/messages.php\"></script>
</head>
<body>

<h1>
  <span class=\"blue\">php</span><span class=\"orange\">MyAdmin</span>
  setup
</h1>

<div id=\"menu\">
  <ul>
    <li>
      <a href=\"index.php";
        // line 28
        echo PhpMyAdmin\Url::getCommon();
        echo "\"";
        echo ((twig_test_empty(($context["formset"] ?? null))) ? (" class=\"active\"") : (""));
        echo ">
        ";
echo _gettext("Overview");
        // line 30
        echo "      </a>
    </li>
    ";
        // line 32
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["pages"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["page"]) {
            // line 33
            echo "      <li>
        <a href=\"index.php";
            // line 34
            echo PhpMyAdmin\Url::getCommon(["page" => "form", "formset" => twig_get_attribute($this->env, $this->source,             // line 36
$context["page"], "formset", [], "any", false, false, false, 36)]);
            // line 37
            echo "\"";
            echo (((($context["formset"] ?? null) == twig_get_attribute($this->env, $this->source, $context["page"], "formset", [], "any", false, false, false, 37))) ? (" class=\"active\"") : (""));
            echo ">
          ";
            // line 38
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["page"], "name", [], "any", false, false, false, 38), "html", null, true);
            echo "
        </a>
      </li>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['page'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 42
        echo "  </ul>
</div>

<div id=\"page\" class=\"setup-page\">
  ";
        // line 46
        $this->displayBlock('content', $context, $blocks);
        // line 47
        echo "</div>

</body>
</html>
";
    }

    // line 46
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    public function getTemplateName()
    {
        return "setup/base.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  119 => 46,  111 => 47,  109 => 46,  103 => 42,  93 => 38,  88 => 37,  86 => 36,  85 => 34,  82 => 33,  78 => 32,  74 => 30,  67 => 28,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "setup/base.twig", "/srv/users/api-development/apps/api-development/public/phpMyAdmin/templates/setup/base.twig");
    }
}
