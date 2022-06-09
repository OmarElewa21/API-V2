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

/* setup/home/index.twig */
class __TwigTemplate_b5824e69f77c62ce17dde06d2b73e607 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "setup/base.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("setup/base.twig", "setup/home/index.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 3
        echo "
<form id=\"select_lang\" method=\"post\">
  ";
        // line 5
        echo PhpMyAdmin\Url::getHiddenInputs();
        echo "
  <bdo lang=\"en\" dir=\"ltr\">
    <label for=\"lang\">
      ";
echo _gettext("Language");
        // line 9
        echo "      ";
        echo (((_gettext("Language") != "Language")) ? (" - Language") : (""));
        echo "
    </label>
  </bdo>
  <br>
  <select id=\"lang\" name=\"lang\" class=\"autosubmit\" lang=\"en\" dir=\"ltr\">
    ";
        // line 14
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["languages"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
            // line 15
            echo "      <option value=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["language"], "code", [], "any", false, false, false, 15), "html", null, true);
            echo "\"";
            echo ((twig_get_attribute($this->env, $this->source, $context["language"], "is_active", [], "any", false, false, false, 15)) ? (" selected") : (""));
            echo ">";
            echo twig_get_attribute($this->env, $this->source, $context["language"], "name", [], "any", false, false, false, 15);
            echo "</option>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['language'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 17
        echo "  </select>
</form>

<h2>";
echo _gettext("Overview");
        // line 20
        echo "</h2>

<a href=\"#\" id=\"show_hidden_messages\" class=\"hide\">
  ";
echo _gettext("Show hidden messages");
        // line 23
        echo " (#MSG_COUNT)
</a>

";
        // line 26
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["messages"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["message"]) {
            // line 27
            echo "  <div class=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["message"], "type", [], "any", false, false, false, 27), "html", null, true);
            echo ((twig_get_attribute($this->env, $this->source, $context["message"], "is_hidden", [], "any", false, false, false, 27)) ? (" hiddenmessage") : (""));
            echo "\" id=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["message"], "id", [], "any", false, false, false, 27), "html", null, true);
            echo "\">
    <h4 class=\"fs-6\">";
            // line 28
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["message"], "title", [], "any", false, false, false, 28), "html", null, true);
            echo "</h4>
    ";
            // line 29
            echo twig_get_attribute($this->env, $this->source, $context["message"], "message", [], "any", false, false, false, 29);
            echo "
  </div>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['message'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 32
        echo "
<fieldset class=\"pma-fieldset simple\">
  <legend>";
echo _gettext("Servers");
        // line 34
        echo "</legend>

  <form method=\"get\" action=\"index.php\" class=\"config-form disableAjax\">
    <input type=\"hidden\" name=\"tab_hash\" value=\"\">
    ";
        // line 38
        if (($context["has_check_page_refresh"] ?? null)) {
            // line 39
            echo "      <input type=\"hidden\" name=\"check_page_refresh\" id=\"check_page_refresh\" value=\"\">
    ";
        }
        // line 41
        echo "    ";
        echo PhpMyAdmin\Url::getHiddenInputs("", "", 0, "server");
        echo "
    ";
        // line 42
        echo PhpMyAdmin\Url::getHiddenFields(["page" => "servers", "mode" => "add"], "", true);
        echo "

  <div class=\"form\">
    ";
        // line 45
        if ((($context["server_count"] ?? null) > 0)) {
            // line 46
            echo "      <table class=\"table w-auto datatable\">
        <tr>
          <th>#</th>
          <th>";
echo _gettext("Name");
            // line 49
            echo "</th>
          <th>";
echo _gettext("Authentication type");
            // line 50
            echo "</th>
          <th colspan=\"2\">DSN</th>
        </tr>

        ";
            // line 54
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["servers"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["server"]) {
                // line 55
                echo "          <tr>
            <td>";
                // line 56
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["server"], "id", [], "any", false, false, false, 56), "html", null, true);
                echo "</td>
            <td>";
                // line 57
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["server"], "name", [], "any", false, false, false, 57), "html", null, true);
                echo "</td>
            <td>";
                // line 58
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["server"], "auth_type", [], "any", false, false, false, 58), "html", null, true);
                echo "</td>
            <td>";
                // line 59
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["server"], "dsn", [], "any", false, false, false, 59), "html", null, true);
                echo "</td>
            <td class=\"text-nowrap\">
              <small>
                <a href=\"";
                // line 62
                echo PhpMyAdmin\Url::getCommon(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["server"], "params", [], "any", false, false, false, 62), "edit", [], "any", false, false, false, 62));
                echo "\">
                  ";
echo _gettext("Edit");
                // line 64
                echo "                </a>
                |
                <a class=\"delete-server\" href=\"";
                // line 66
                echo PhpMyAdmin\Url::getCommon(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["server"], "params", [], "any", false, false, false, 66), "remove", [], "any", false, false, false, 66));
                echo "\" data-post=\"";
                // line 67
                echo PhpMyAdmin\Url::getCommon(["token" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["server"], "params", [], "any", false, false, false, 67), "token", [], "any", false, false, false, 67)], "", false);
                echo "\">
                  ";
echo _gettext("Delete");
                // line 69
                echo "                </a>
              </small>
            </td>
          </tr>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['server'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 74
            echo "      </table>
    ";
        } else {
            // line 76
            echo "      <table class=\"table mb-0\">
        <tr>
          <td>
            <em>";
echo _gettext("There are no configured servers");
            // line 79
            echo "</em>
          </td>
        </tr>
      </table>
    ";
        }
        // line 84
        echo "
    <table class=\"table mb-0\">
      <tr>
        <td class=\"lastrow text-start\">
          <input type=\"submit\" name=\"submit\" value=\"";
echo _gettext("New server");
        // line 88
        echo "\">
        </td>
      </tr>
    </table>
  </div>

  </form>
</fieldset>

<fieldset class=\"pma-fieldset simple\">
  <legend>";
echo _gettext("Configuration file");
        // line 98
        echo "</legend>

  <form method=\"post\" action=\"config.php\" class=\"config-form disableAjax\">
    <input type=\"hidden\" name=\"tab_hash\" value=\"\">
    ";
        // line 102
        if (($context["has_check_page_refresh"] ?? null)) {
            // line 103
            echo "      <input type=\"hidden\" name=\"check_page_refresh\" id=\"check_page_refresh\" value=\"\">
    ";
        }
        // line 105
        echo "    ";
        echo PhpMyAdmin\Url::getHiddenInputs("", "", 0, "server");
        echo "

  <table class=\"table table-borderless\">
    <tr>
      <th>
        <label for=\"DefaultLang\">";
echo _gettext("Default language");
        // line 110
        echo "</label>
        <span class=\"doc\">
          <a href=\"";
        // line 112
        echo PhpMyAdmin\Html\MySQLDocumentation::getDocumentationLink("config", "cfg_DefaultLang", "../");
        echo "\" target=\"_blank\" rel=\"noreferrer noopener\">";
        // line 113
        echo PhpMyAdmin\Html\Generator::getImage("b_help", _gettext("Documentation"));
        // line 114
        echo "</a>
        </span>
      </th>
      <td>
        <select name=\"DefaultLang\" id=\"DefaultLang\" class=\"w-75\">
          ";
        // line 119
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["languages"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
            // line 120
            echo "            <option value=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["language"], "code", [], "any", false, false, false, 120), "html", null, true);
            echo "\"";
            echo ((twig_get_attribute($this->env, $this->source, $context["language"], "is_active", [], "any", false, false, false, 120)) ? (" selected") : (""));
            echo ">";
            echo twig_get_attribute($this->env, $this->source, $context["language"], "name", [], "any", false, false, false, 120);
            echo "</option>
          ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['language'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 122
        echo "        </select>
      </td>
    </tr>

    <tr>
      <th>
        <label for=\"ServerDefault\">";
echo _gettext("Default server");
        // line 128
        echo "</label>
        <span class=\"doc\">
          <a href=\"";
        // line 130
        echo PhpMyAdmin\Html\MySQLDocumentation::getDocumentationLink("config", "cfg_ServerDefault", "../");
        echo "\" target=\"_blank\" rel=\"noreferrer noopener\">";
        // line 131
        echo PhpMyAdmin\Html\Generator::getImage("b_help", _gettext("Documentation"));
        // line 132
        echo "</a>
        </span>
      </th>
      <td>
        <select name=\"ServerDefault\" id=\"ServerDefault\" class=\"w-75\">
          ";
        // line 137
        if ((($context["server_count"] ?? null) > 0)) {
            // line 138
            echo "            ";
            if ((($context["server_count"] ?? null) > 1)) {
                // line 139
                echo "              <option value=\"0\">";
echo _gettext("let the user choose");
                echo "</option>
              <option value=\"\" disabled>------------------------------</option>
            ";
            }
            // line 142
            echo "            ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["servers"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["server"]) {
                // line 143
                echo "              <option value=\"";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["server"], "id", [], "any", false, false, false, 143), "html", null, true);
                echo "\"";
                echo (((twig_get_attribute($this->env, $this->source, $context["server"], "id", [], "any", false, false, false, 143) == 1)) ? (" selected") : (""));
                echo ">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["server"], "name", [], "any", false, false, false, 143), "html", null, true);
                echo " [";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["server"], "id", [], "any", false, false, false, 143), "html", null, true);
                echo "]</option>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['server'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 145
            echo "          ";
        } else {
            // line 146
            echo "            <option value=\"1\">";
echo _gettext("- none -");
            echo "</option>
          ";
        }
        // line 148
        echo "        </select>
      </td>
    </tr>

    <tr>
      <th><label for=\"eol\">";
echo _gettext("End of line");
        // line 153
        echo "</label></th>
      <td>
        <select name=\"eol\" id=\"eol\" class=\"w-75\">
          <option value=\"unix\"";
        // line 156
        echo (((($context["eol"] ?? null) == "unix")) ? (" selected") : (""));
        echo ">UNIX / Linux (\\n)</option>
          <option value=\"win\"";
        // line 157
        echo (((($context["eol"] ?? null) == "win")) ? (" selected") : (""));
        echo ">Windows (\\r\\n)</option>
        </select>
      </td>
    </tr>

    <tr>
      <td colspan=\"2\" class=\"lastrow text-start\">
        <input type=\"submit\" name=\"submit_display\" value=\"";
echo _gettext("Display");
        // line 164
        echo "\">
        <input type=\"submit\" name=\"submit_download\" value=\"";
echo _gettext("Download");
        // line 165
        echo "\">
        <input class=\"red\" type=\"submit\" name=\"submit_clear\" value=\"";
echo _gettext("Clear");
        // line 166
        echo "\">
      </td>
    </tr>
  </table>

  </form>
</fieldset>

<div id=\"footer\">
  <a href=\"../url.php?url=https://www.phpmyadmin.net/\">";
echo _gettext("phpMyAdmin homepage");
        // line 175
        echo "</a>
  <a href=\"../url.php?url=https://www.phpmyadmin.net/donate/\">";
echo _gettext("Donate");
        // line 176
        echo "</a>
  <a href=\"";
        // line 177
        echo PhpMyAdmin\Url::getCommon(["version_check" => "1"]);
        echo "\">";
echo _gettext("Check for latest version");
        echo "</a>
</div>

";
    }

    public function getTemplateName()
    {
        return "setup/home/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  440 => 177,  437 => 176,  433 => 175,  421 => 166,  417 => 165,  413 => 164,  402 => 157,  398 => 156,  393 => 153,  385 => 148,  379 => 146,  376 => 145,  361 => 143,  356 => 142,  349 => 139,  346 => 138,  344 => 137,  337 => 132,  335 => 131,  332 => 130,  328 => 128,  319 => 122,  306 => 120,  302 => 119,  295 => 114,  293 => 113,  290 => 112,  286 => 110,  276 => 105,  272 => 103,  270 => 102,  264 => 98,  251 => 88,  244 => 84,  237 => 79,  231 => 76,  227 => 74,  217 => 69,  212 => 67,  209 => 66,  205 => 64,  200 => 62,  194 => 59,  190 => 58,  186 => 57,  182 => 56,  179 => 55,  175 => 54,  169 => 50,  165 => 49,  159 => 46,  157 => 45,  151 => 42,  146 => 41,  142 => 39,  140 => 38,  134 => 34,  129 => 32,  120 => 29,  116 => 28,  108 => 27,  104 => 26,  99 => 23,  93 => 20,  87 => 17,  74 => 15,  70 => 14,  61 => 9,  54 => 5,  50 => 3,  46 => 2,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "setup/home/index.twig", "/srv/users/api-development/apps/api-development/public/phpMyAdmin/templates/setup/home/index.twig");
    }
}
