<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* @Live/index.twig */
class __TwigTemplate_6e47d31473f35e5830861f7e694f9236 extends Template
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
        yield "<div
    vue-entry=\"Live.LivePage\"
    disable-link=\"";
        // line 3
        yield \Piwik\piwik_escape_filter($this->env, json_encode(Twig\Extension\CoreExtension::default(((isset($context["disableLink"]) || array_key_exists("disableLink", $context) ? $context["disableLink"] : (function () { throw new RuntimeError('Variable "disableLink" does not exist.', 3, $this->source); })()) == 1), null)), "html", null, true);
        yield "\"
    visitors=\"";
        // line 4
        yield \Piwik\piwik_escape_filter($this->env, json_encode(Twig\Extension\CoreExtension::replace(((array_key_exists("visitors", $context)) ? (Twig\Extension\CoreExtension::default((isset($context["visitors"]) || array_key_exists("visitors", $context) ? $context["visitors"] : (function () { throw new RuntimeError('Variable "visitors" does not exist.', 4, $this->source); })()), null)) : (null)), ["{{" => "{&lbrace;"])), "html", null, true);
        yield "\"
    initial-total-visitors=\"";
        // line 5
        yield \Piwik\piwik_escape_filter($this->env, json_encode(((array_key_exists("initialTotalVisitors", $context)) ? (Twig\Extension\CoreExtension::default((isset($context["initialTotalVisitors"]) || array_key_exists("initialTotalVisitors", $context) ? $context["initialTotalVisitors"] : (function () { throw new RuntimeError('Variable "initialTotalVisitors" does not exist.', 5, $this->source); })()), null)) : (null))), "html", null, true);
        yield "\"
    live-refresh-after-ms=\"";
        // line 6
        yield \Piwik\piwik_escape_filter($this->env, json_encode(((array_key_exists("liveRefreshAfterMs", $context)) ? (Twig\Extension\CoreExtension::default((isset($context["liveRefreshAfterMs"]) || array_key_exists("liveRefreshAfterMs", $context) ? $context["liveRefreshAfterMs"] : (function () { throw new RuntimeError('Variable "liveRefreshAfterMs" does not exist.', 6, $this->source); })()), null)) : (null))), "html", null, true);
        yield "\"
    is-widgetized=\"";
        // line 7
        yield \Piwik\piwik_escape_filter($this->env, json_encode(Twig\Extension\CoreExtension::default(((isset($context["isWidgetized"]) || array_key_exists("isWidgetized", $context) ? $context["isWidgetized"] : (function () { throw new RuntimeError('Variable "isWidgetized" does not exist.', 7, $this->source); })()) == 1), null)), "html", null, true);
        yield "\"
></div>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@Live/index.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  58 => 7,  54 => 6,  50 => 5,  46 => 4,  42 => 3,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<div
    vue-entry=\"Live.LivePage\"
    disable-link=\"{{ (disableLink == 1)|default(null)|json_encode }}\"
    visitors=\"{{ visitors|default(null)|replace({'{{': '{&lbrace;'})|json_encode }}\"
    initial-total-visitors=\"{{ initialTotalVisitors|default(null)|json_encode }}\"
    live-refresh-after-ms=\"{{ liveRefreshAfterMs|default(null)|json_encode }}\"
    is-widgetized=\"{{ (isWidgetized == 1)|default(null)|json_encode }}\"
></div>
", "@Live/index.twig", "/var/www/htdocs/climbing/plugins/Live/templates/index.twig");
    }
}
