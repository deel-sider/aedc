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

/* @CoreHome/_dataTable.twig */
class __TwigTemplate_f12518d93fb37551455ee9dae2c561ee extends Template
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
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 1, $this->source); })()), "show_visualization_only", [], "any", false, false, false, 1)) {
            // line 2
            yield "    ";
            yield from             $this->loadTemplate((isset($context["visualizationTemplate"]) || array_key_exists("visualizationTemplate", $context) ? $context["visualizationTemplate"] : (function () { throw new RuntimeError('Variable "visualizationTemplate" does not exist.', 2, $this->source); })()), "@CoreHome/_dataTable.twig", 2)->unwrap()->yield($context);
        } else {
            // line 5
            $context["isDataTableEmpty"] = (( !array_key_exists("dataTable", $context) || (null === (isset($context["dataTable"]) || array_key_exists("dataTable", $context) ? $context["dataTable"] : (function () { throw new RuntimeError('Variable "dataTable" does not exist.', 5, $this->source); })()))) || ((array_key_exists("dataTableHasNoData", $context)) ? (Twig\Extension\CoreExtension::default((isset($context["dataTableHasNoData"]) || array_key_exists("dataTableHasNoData", $context) ? $context["dataTableHasNoData"] : (function () { throw new RuntimeError('Variable "dataTableHasNoData" does not exist.', 5, $this->source); })()), false)) : (false)));
            // line 6
            yield "
";
            // line 7
            $context["showCardAsContentBlock"] = ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 7, $this->source); })()), "show_as_content_block", [], "any", false, false, false, 7) && CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 7, $this->source); })()), "show_title", [], "any", false, false, false, 7)) &&  !(isset($context["isWidget"]) || array_key_exists("isWidget", $context) ? $context["isWidget"] : (function () { throw new RuntimeError('Variable "isWidget" does not exist.', 7, $this->source); })()));
            // line 8
            $context["showOnlyTitleWithoutCard"] = (( !(isset($context["showCardAsContentBlock"]) || array_key_exists("showCardAsContentBlock", $context) ? $context["showCardAsContentBlock"] : (function () { throw new RuntimeError('Variable "showCardAsContentBlock" does not exist.', 8, $this->source); })()) && CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 8, $this->source); })()), "title", [], "any", false, false, false, 8)) && CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 8, $this->source); })()), "show_title", [], "any", false, false, false, 8));
            // line 9
            yield "
";
            // line 15
            $context["showTableActionsInHeader"] = (CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 15, $this->source); })()), "show_footer", [], "any", false, false, false, 15) && CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 15, $this->source); })()), "show_footer_icons", [], "any", false, false, false, 15));
            // line 16
            yield "
";
            // line 17
            if ((isset($context["showCardAsContentBlock"]) || array_key_exists("showCardAsContentBlock", $context) ? $context["showCardAsContentBlock"] : (function () { throw new RuntimeError('Variable "showCardAsContentBlock" does not exist.', 17, $this->source); })())) {
                // line 18
                yield "<div class=\"card\">
<div class=\"card-content\">
    ";
                // line 20
                if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 20, $this->source); })()), "title", [], "any", false, false, false, 20)) {
                    // line 21
                    yield "        <h2 class=\"card-title\"
            ";
                    // line 22
                    if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 22, $this->source); })()), "title_edit_entity_url", [], "any", false, false, false, 22)) {
                        yield "edit-url=\"";
                        yield \Piwik\piwik_escape_filter($this->env, CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 22, $this->source); })()), "title_edit_entity_url", [], "any", false, false, false, 22), "html", null, true);
                        yield "\"";
                    }
                    // line 23
                    yield "            report-generated=\"";
                    if (array_key_exists("reportLastUpdatedMessage", $context)) {
                        yield (isset($context["reportLastUpdatedMessage"]) || array_key_exists("reportLastUpdatedMessage", $context) ? $context["reportLastUpdatedMessage"] : (function () { throw new RuntimeError('Variable "reportLastUpdatedMessage" does not exist.', 23, $this->source); })());
                    }
                    yield "\"
              vue-entry=\"CoreHome.EnrichedHeadline\"
        >";
                    // line 25
                    yield \Piwik\piwik_escape_filter($this->env, CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 25, $this->source); })()), "title", [], "any", false, false, false, 25), "html", null, true);
                    yield "</h2>
    ";
                }
            } elseif (            // line 27
(isset($context["showOnlyTitleWithoutCard"]) || array_key_exists("showOnlyTitleWithoutCard", $context) ? $context["showOnlyTitleWithoutCard"] : (function () { throw new RuntimeError('Variable "showOnlyTitleWithoutCard" does not exist.', 27, $this->source); })())) {
                // line 28
                yield "    <div>
    <h2>";
                // line 29
                yield \Piwik\piwik_escape_filter($this->env, CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 29, $this->source); })()), "title", [], "any", false, false, false, 29), "html", null, true);
                yield "</h2>
";
            }
            // line 31
            yield "
";
            // line 32
            $context["showCardTableIsEmpty"] = (( !CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 32, $this->source); })()), "show_as_content_block", [], "any", false, false, false, 32) && (isset($context["isDataTableEmpty"]) || array_key_exists("isDataTableEmpty", $context) ? $context["isDataTableEmpty"] : (function () { throw new RuntimeError('Variable "isDataTableEmpty" does not exist.', 32, $this->source); })())) &&  !(isset($context["isWidget"]) || array_key_exists("isWidget", $context) ? $context["isWidget"] : (function () { throw new RuntimeError('Variable "isWidget" does not exist.', 32, $this->source); })()));
            // line 33
            if ((isset($context["showCardTableIsEmpty"]) || array_key_exists("showCardTableIsEmpty", $context) ? $context["showCardTableIsEmpty"] : (function () { throw new RuntimeError('Variable "showCardTableIsEmpty" does not exist.', 33, $this->source); })())) {
                // line 34
                yield "    <div class=\"card\">
    <div class=\"card-content\">
";
            }
            // line 37
            yield "
";
            // line 38
            $context["summaryRowId"] = Twig\Extension\CoreExtension::constant("Piwik\\DataTable::ID_SUMMARY_ROW");
            // line 39
            $context["isSubtable"] = (CoreExtension::getAttribute($this->env, $this->source, ($context["javascriptVariablesToSet"] ?? null), "idSubtable", [], "any", true, true, false, 39) && (CoreExtension::getAttribute($this->env, $this->source, (isset($context["javascriptVariablesToSet"]) || array_key_exists("javascriptVariablesToSet", $context) ? $context["javascriptVariablesToSet"] : (function () { throw new RuntimeError('Variable "javascriptVariablesToSet" does not exist.', 39, $this->source); })()), "idSubtable", [], "any", false, false, false, 39) != 0));
            // line 40
            yield "<div class=\"dataTable ";
            yield \Piwik\piwik_escape_filter($this->env, (isset($context["visualizationCssClass"]) || array_key_exists("visualizationCssClass", $context) ? $context["visualizationCssClass"] : (function () { throw new RuntimeError('Variable "visualizationCssClass" does not exist.', 40, $this->source); })()), "html", null, true);
            yield " ";
            yield \Piwik\piwik_escape_filter($this->env, ((CoreExtension::getAttribute($this->env, $this->source, ($context["properties"] ?? null), "datatable_css_class", [], "any", true, true, false, 40)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["properties"] ?? null), "datatable_css_class", [], "any", false, false, false, 40), "")) : ("")), "html", null, true);
            if ((isset($context["isSubtable"]) || array_key_exists("isSubtable", $context) ? $context["isSubtable"] : (function () { throw new RuntimeError('Variable "isSubtable" does not exist.', 40, $this->source); })())) {
                yield " subDataTable";
            }
            if (((array_key_exists("isComparing", $context)) ? (Twig\Extension\CoreExtension::default((isset($context["isComparing"]) || array_key_exists("isComparing", $context) ? $context["isComparing"] : (function () { throw new RuntimeError('Variable "isComparing" does not exist.', 40, $this->source); })()), false)) : (false))) {
                yield " isComparing";
            }
            if ((isset($context["isDataTableEmpty"]) || array_key_exists("isDataTableEmpty", $context) ? $context["isDataTableEmpty"] : (function () { throw new RuntimeError('Variable "isDataTableEmpty" does not exist.', 40, $this->source); })())) {
                yield " isDataTableEmpty";
            }
            yield "\"
     data-table-type=\"";
            // line 41
            yield \Piwik\piwik_escape_filter($this->env, CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 41, $this->source); })()), "datatable_js_type", [], "any", false, false, false, 41), "html", null, true);
            yield "\"
     data-report=\"";
            // line 42
            yield \Piwik\piwik_escape_filter($this->env, CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 42, $this->source); })()), "report_id", [], "any", false, false, false, 42), "html", null, true);
            yield "\"
     data-report-metadata=\"";
            // line 43
            yield \Piwik\piwik_escape_filter($this->env, json_encode((isset($context["reportMetdadata"]) || array_key_exists("reportMetdadata", $context) ? $context["reportMetdadata"] : (function () { throw new RuntimeError('Variable "reportMetdadata" does not exist.', 43, $this->source); })())), "html_attr");
            yield "\"
     data-props=\"";
            // line 44
            if (Twig\Extension\CoreExtension::testEmpty((isset($context["clientSideProperties"]) || array_key_exists("clientSideProperties", $context) ? $context["clientSideProperties"] : (function () { throw new RuntimeError('Variable "clientSideProperties" does not exist.', 44, $this->source); })()))) {
                yield "{}";
            } else {
                yield \Piwik\piwik_escape_filter($this->env, json_encode((isset($context["clientSideProperties"]) || array_key_exists("clientSideProperties", $context) ? $context["clientSideProperties"] : (function () { throw new RuntimeError('Variable "clientSideProperties" does not exist.', 44, $this->source); })())), "html", null, true);
            }
            yield "\"
     data-params=\"";
            // line 45
            if (Twig\Extension\CoreExtension::testEmpty((isset($context["clientSideParameters"]) || array_key_exists("clientSideParameters", $context) ? $context["clientSideParameters"] : (function () { throw new RuntimeError('Variable "clientSideParameters" does not exist.', 45, $this->source); })()))) {
                yield "{}";
            } else {
                yield \Piwik\piwik_escape_filter($this->env, json_encode((isset($context["clientSideParameters"]) || array_key_exists("clientSideParameters", $context) ? $context["clientSideParameters"] : (function () { throw new RuntimeError('Variable "clientSideParameters" does not exist.', 45, $this->source); })())), "html", null, true);
            }
            yield "\">

    ";
            // line 47
            if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 47, $this->source); })()), "description", [], "any", false, false, false, 47)) {
                // line 48
                yield "        <div class=\"card-description\">";
                yield \Piwik\piwik_escape_filter($this->env, CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 48, $this->source); })()), "description", [], "any", false, false, false, 48), "html", null, true);
                yield "</div>
    ";
            }
            // line 50
            yield "
    <div class=\"reportDocumentation\" data-content=\"";
            // line 51
            yield \Piwik\piwik_escape_filter($this->env, ((CoreExtension::getAttribute($this->env, $this->source, ($context["properties"] ?? null), "documentation", [], "any", true, true, false, 51)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["properties"] ?? null), "documentation", [], "any", false, false, false, 51))) : ("")), "html_attr");
            yield "\">
        ";
            // line 52
            if ( !Twig\Extension\CoreExtension::testEmpty(((CoreExtension::getAttribute($this->env, $this->source, ($context["properties"] ?? null), "onlineGuideUrl", [], "any", true, true, false, 52)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["properties"] ?? null), "onlineGuideUrl", [], "any", false, false, false, 52))) : ("")))) {
                yield "<a href=\"";
                yield \Piwik\piwik_escape_filter($this->env, $this->env->getFilter('safelink')->getCallable()(CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 52, $this->source); })()), "onlineGuideUrl", [], "any", false, false, false, 52)), "html_attr");
                yield "\" target=\"_blank\" rel=\"noreferrer noopener\" class=\"onlineGuide\">";
                yield \Piwik\piwik_escape_filter($this->env, $this->env->getFilter('translate')->getCallable()("CoreHome_ReadMoreOnlineGuide"), "html", null, true);
                yield "</a>";
            }
            // line 53
            yield "        ";
            if ((array_key_exists("reportLastUpdatedMessage", $context) && (isset($context["reportLastUpdatedMessage"]) || array_key_exists("reportLastUpdatedMessage", $context) ? $context["reportLastUpdatedMessage"] : (function () { throw new RuntimeError('Variable "reportLastUpdatedMessage" does not exist.', 53, $this->source); })()))) {
                yield "<span class=\"helpDate\">";
                yield (isset($context["reportLastUpdatedMessage"]) || array_key_exists("reportLastUpdatedMessage", $context) ? $context["reportLastUpdatedMessage"] : (function () { throw new RuntimeError('Variable "reportLastUpdatedMessage" does not exist.', 53, $this->source); })());
                yield "</span>";
            }
            // line 54
            yield "    </div>

    <div class=\"dataTableWrapper\">
        ";
            // line 57
            if (array_key_exists("error", $context)) {
                // line 58
                yield "            <div vue-entry=\"CoreHome.Alert\" severity=\"danger\">";
                yield \Piwik\piwik_escape_filter($this->env, CoreExtension::getAttribute($this->env, $this->source, (isset($context["error"]) || array_key_exists("error", $context) ? $context["error"] : (function () { throw new RuntimeError('Variable "error" does not exist.', 58, $this->source); })()), "message", [], "any", false, false, false, 58), "html", null, true);
                yield "</div>
        ";
            } else {
                // line 60
                yield "            ";
                if ((isset($context["showTableActionsInHeader"]) || array_key_exists("showTableActionsInHeader", $context) ? $context["showTableActionsInHeader"] : (function () { throw new RuntimeError('Variable "showTableActionsInHeader" does not exist.', 60, $this->source); })())) {
                    // line 61
                    yield "                <div class=\"row dataTableHeaderControls\">
                    <div class=\"col dataTableControls s12\">
                        ";
                    // line 63
                    yield from                     $this->loadTemplate("@CoreHome/_dataTableActions.twig", "@CoreHome/_dataTable.twig", 63)->unwrap()->yield(CoreExtension::merge($context, ["placement" => "top"]));
                    // line 64
                    yield "                    </div>
                </div>
            ";
                }
                // line 67
                yield "
            ";
                // line 68
                if ((CoreExtension::getAttribute($this->env, $this->source, ($context["properties"] ?? null), "show_header_message", [], "any", true, true, false, 68) &&  !Twig\Extension\CoreExtension::testEmpty(CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 68, $this->source); })()), "show_header_message", [], "any", false, false, false, 68)))) {
                    // line 69
                    yield "                <div class='datatableHeaderMessage'>";
                    yield CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 69, $this->source); })()), "show_header_message", [], "any", false, false, false, 69);
                    yield "</div>
            ";
                }
                // line 71
                yield "
            ";
                // line 72
                if ((isset($context["isDataTableEmpty"]) || array_key_exists("isDataTableEmpty", $context) ? $context["isDataTableEmpty"] : (function () { throw new RuntimeError('Variable "isDataTableEmpty" does not exist.', 72, $this->source); })())) {
                    // line 73
                    yield "                <div class=\"pk-emptyDataTable\">
                ";
                    // line 74
                    if ((array_key_exists("showReportDataWasPurgedMessage", $context) && (isset($context["showReportDataWasPurgedMessage"]) || array_key_exists("showReportDataWasPurgedMessage", $context) ? $context["showReportDataWasPurgedMessage"] : (function () { throw new RuntimeError('Variable "showReportDataWasPurgedMessage" does not exist.', 74, $this->source); })()))) {
                        // line 75
                        yield "                    ";
                        yield \Piwik\piwik_escape_filter($this->env, $this->env->getFilter('translate')->getCallable()("CoreHome_DataForThisReportHasBeenPurged", (isset($context["deleteReportsOlderThan"]) || array_key_exists("deleteReportsOlderThan", $context) ? $context["deleteReportsOlderThan"] : (function () { throw new RuntimeError('Variable "deleteReportsOlderThan" does not exist.', 75, $this->source); })())), "html", null, true);
                        yield "
                ";
                    } elseif ((                    // line 76
array_key_exists("showPluginArchiveDisabled", $context) && (isset($context["showPluginArchiveDisabled"]) || array_key_exists("showPluginArchiveDisabled", $context) ? $context["showPluginArchiveDisabled"] : (function () { throw new RuntimeError('Variable "showPluginArchiveDisabled" does not exist.', 76, $this->source); })()))) {
                        // line 77
                        yield "                    ";
                        yield $this->env->getFilter('translate')->getCallable()("CoreHome_DataForThisReportHasBeenDisabled", $this->env->getFunction('externallink')->getCallable()("https://matomo.org/faq/how-to-disable-archiving-the-segment-reports-for-specific-plugins"), "</a>");
                        yield "
                ";
                    } elseif (CoreExtension::getAttribute($this->env, $this->source,                     // line 78
(isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 78, $this->source); })()), "no_data_message", [], "any", false, false, false, 78)) {
                        // line 79
                        yield "                    ";
                        yield CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 79, $this->source); })()), "no_data_message", [], "any", false, false, false, 79);
                        yield "
                ";
                    } else {
                        // line 81
                        yield "                    ";
                        yield \Piwik\piwik_escape_filter($this->env, $this->env->getFilter('translate')->getCallable()("CoreHome_ThereIsNoDataForThisReport"), "html", null, true);
                        yield "
                ";
                    }
                    // line 83
                    yield "                </div>
            ";
                } else {
                    // line 85
                    yield "                ";
                    yield from                     $this->loadTemplate((isset($context["visualizationTemplate"]) || array_key_exists("visualizationTemplate", $context) ? $context["visualizationTemplate"] : (function () { throw new RuntimeError('Variable "visualizationTemplate" does not exist.', 85, $this->source); })()), "@CoreHome/_dataTable.twig", 85)->unwrap()->yield($context);
                    // line 86
                    yield "            ";
                }
                // line 87
                yield "
            ";
                // line 88
                if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 88, $this->source); })()), "show_footer", [], "any", false, false, false, 88)) {
                    // line 89
                    yield "                ";
                    yield from                     $this->loadTemplate("@CoreHome/_dataTableFooter.twig", "@CoreHome/_dataTable.twig", 89)->unwrap()->yield($context);
                    // line 90
                    yield "            ";
                }
                // line 91
                yield "
            ";
                // line 92
                yield from                 $this->loadTemplate("@CoreHome/_dataTableJS.twig", "@CoreHome/_dataTable.twig", 92)->unwrap()->yield(CoreExtension::merge($context, ["reportId" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 92, $this->source); })()), "report_id", [], "any", false, false, false, 92)]));
                // line 93
                yield "        ";
            }
            // line 94
            yield "    </div>
</div>

";
            // line 97
            if (( !Twig\Extension\CoreExtension::testEmpty((isset($context["notifications"]) || array_key_exists("notifications", $context) ? $context["notifications"] : (function () { throw new RuntimeError('Variable "notifications" does not exist.', 97, $this->source); })())) && Twig\Extension\CoreExtension::length($this->env->getCharset(), (isset($context["notifications"]) || array_key_exists("notifications", $context) ? $context["notifications"] : (function () { throw new RuntimeError('Variable "notifications" does not exist.', 97, $this->source); })())))) {
                // line 98
                yield "    ";
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable((isset($context["notifications"]) || array_key_exists("notifications", $context) ? $context["notifications"] : (function () { throw new RuntimeError('Variable "notifications" does not exist.', 98, $this->source); })()));
                foreach ($context['_seq'] as $context["notificationId"] => $context["n"]) {
                    // line 99
                    yield "
        ";
                    // line 100
                    yield $this->env->getFilter('notification')->getCallable()(CoreExtension::getAttribute($this->env, $this->source, $context["n"], "message", [], "any", false, false, false, 100), ["id" => $context["notificationId"], "type" => CoreExtension::getAttribute($this->env, $this->source, $context["n"], "type", [], "any", false, false, false, 100), "title" => CoreExtension::getAttribute($this->env, $this->source, $context["n"], "title", [], "any", false, false, false, 100), "noclear" => CoreExtension::getAttribute($this->env, $this->source, $context["n"], "hasNoClear", [], "any", false, false, false, 100), "context" => CoreExtension::getAttribute($this->env, $this->source, $context["n"], "context", [], "any", false, false, false, 100), "raw" => CoreExtension::getAttribute($this->env, $this->source, $context["n"], "raw", [], "any", false, false, false, 100)], false);
                    yield "

    ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['notificationId'], $context['n'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
            }
            // line 104
            yield "
";
            // line 105
            if ((isset($context["showCardTableIsEmpty"]) || array_key_exists("showCardTableIsEmpty", $context) ? $context["showCardTableIsEmpty"] : (function () { throw new RuntimeError('Variable "showCardTableIsEmpty" does not exist.', 105, $this->source); })())) {
                // line 106
                yield "    </div></div>
";
            }
            // line 108
            yield "
";
            // line 109
            if ((isset($context["showCardAsContentBlock"]) || array_key_exists("showCardAsContentBlock", $context) ? $context["showCardAsContentBlock"] : (function () { throw new RuntimeError('Variable "showCardAsContentBlock" does not exist.', 109, $this->source); })())) {
                // line 110
                yield "    </div></div>
";
            } elseif (            // line 111
(isset($context["showOnlyTitleWithoutCard"]) || array_key_exists("showOnlyTitleWithoutCard", $context) ? $context["showOnlyTitleWithoutCard"] : (function () { throw new RuntimeError('Variable "showOnlyTitleWithoutCard" does not exist.', 111, $this->source); })())) {
                // line 112
                yield "    </div>
";
            }
        }
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@CoreHome/_dataTable.twig";
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
        return array (  342 => 112,  340 => 111,  337 => 110,  335 => 109,  332 => 108,  328 => 106,  326 => 105,  323 => 104,  313 => 100,  310 => 99,  305 => 98,  303 => 97,  298 => 94,  295 => 93,  293 => 92,  290 => 91,  287 => 90,  284 => 89,  282 => 88,  279 => 87,  276 => 86,  273 => 85,  269 => 83,  263 => 81,  257 => 79,  255 => 78,  250 => 77,  248 => 76,  243 => 75,  241 => 74,  238 => 73,  236 => 72,  233 => 71,  227 => 69,  225 => 68,  222 => 67,  217 => 64,  215 => 63,  211 => 61,  208 => 60,  202 => 58,  200 => 57,  195 => 54,  188 => 53,  180 => 52,  176 => 51,  173 => 50,  167 => 48,  165 => 47,  156 => 45,  148 => 44,  144 => 43,  140 => 42,  136 => 41,  120 => 40,  118 => 39,  116 => 38,  113 => 37,  108 => 34,  106 => 33,  104 => 32,  101 => 31,  96 => 29,  93 => 28,  91 => 27,  86 => 25,  78 => 23,  72 => 22,  69 => 21,  67 => 20,  63 => 18,  61 => 17,  58 => 16,  56 => 15,  53 => 9,  51 => 8,  49 => 7,  46 => 6,  44 => 5,  40 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% if properties.show_visualization_only %}
    {% include visualizationTemplate %}
{%- else -%}

{% set isDataTableEmpty = (dataTable is not defined or dataTable is null or dataTableHasNoData|default(false)) %}

{% set showCardAsContentBlock = (properties.show_as_content_block and properties.show_title and not isWidget) %}
{% set showOnlyTitleWithoutCard = not showCardAsContentBlock and properties.title and properties.show_title %}

{#
using the show_footer to control the header actions as well since this is going to be refactored with the goal
of moving the table actions to the top of the report for all reports, at which point the config for the report
can be renamed to show_header and show_header_icons
#}
{% set showTableActionsInHeader = properties.show_footer and properties.show_footer_icons %}

{% if showCardAsContentBlock  %}
<div class=\"card\">
<div class=\"card-content\">
    {% if properties.title %}
        <h2 class=\"card-title\"
            {% if properties.title_edit_entity_url %}edit-url=\"{{ properties.title_edit_entity_url }}\"{% endif %}
            report-generated=\"{% if reportLastUpdatedMessage is defined %}{{ reportLastUpdatedMessage|raw }}{% endif %}\"
              vue-entry=\"CoreHome.EnrichedHeadline\"
        >{{ properties.title }}</h2>
    {% endif %}
{% elseif showOnlyTitleWithoutCard %}
    <div>
    <h2>{{ properties.title }}</h2>
{% endif %}

{% set showCardTableIsEmpty = not properties.show_as_content_block and isDataTableEmpty and not isWidget %}
{% if showCardTableIsEmpty %}
    <div class=\"card\">
    <div class=\"card-content\">
{% endif %}

{% set summaryRowId = constant('Piwik\\\\DataTable::ID_SUMMARY_ROW') %}{# ID_SUMMARY_ROW #}
{% set isSubtable = javascriptVariablesToSet.idSubtable is defined and javascriptVariablesToSet.idSubtable != 0 %}
<div class=\"dataTable {{ visualizationCssClass }} {{ properties.datatable_css_class|default('') }}{% if isSubtable %} subDataTable{% endif %}{% if isComparing|default(false) %} isComparing{% endif %}{% if isDataTableEmpty %} isDataTableEmpty{% endif %}\"
     data-table-type=\"{{ properties.datatable_js_type }}\"
     data-report=\"{{ properties.report_id }}\"
     data-report-metadata=\"{{ reportMetdadata|json_encode|e('html_attr') }}\"
     data-props=\"{% if clientSideProperties is empty %}{}{% else %}{{ clientSideProperties|json_encode }}{% endif %}\"
     data-params=\"{% if clientSideParameters is empty %}{}{% else %}{{ clientSideParameters|json_encode }}{% endif %}\">

    {% if properties.description %}
        <div class=\"card-description\">{{ properties.description }}</div>
    {% endif %}

    <div class=\"reportDocumentation\" data-content=\"{{ properties.documentation|default|e('html_attr') }}\">
        {% if properties.onlineGuideUrl|default is not empty %}<a href=\"{{ properties.onlineGuideUrl|safelink|e('html_attr') }}\" target=\"_blank\" rel=\"noreferrer noopener\" class=\"onlineGuide\">{{ 'CoreHome_ReadMoreOnlineGuide'|translate }}</a>{% endif %}
        {% if reportLastUpdatedMessage is defined and reportLastUpdatedMessage %}<span class=\"helpDate\">{{ reportLastUpdatedMessage|raw }}</span>{% endif %}
    </div>

    <div class=\"dataTableWrapper\">
        {% if error is defined %}
            <div vue-entry=\"CoreHome.Alert\" severity=\"danger\">{{ error.message }}</div>
        {% else %}
            {% if showTableActionsInHeader %}
                <div class=\"row dataTableHeaderControls\">
                    <div class=\"col dataTableControls s12\">
                        {% include \"@CoreHome/_dataTableActions.twig\" with { placement: 'top' } %}
                    </div>
                </div>
            {% endif %}

            {% if properties.show_header_message is defined and properties.show_header_message is not empty %}
                <div class='datatableHeaderMessage'>{{ properties.show_header_message | raw }}</div>
            {% endif %}

            {% if isDataTableEmpty %}
                <div class=\"pk-emptyDataTable\">
                {% if showReportDataWasPurgedMessage is defined and showReportDataWasPurgedMessage %}
                    {{ 'CoreHome_DataForThisReportHasBeenPurged'|translate(deleteReportsOlderThan) }}
                {% elseif showPluginArchiveDisabled is defined and showPluginArchiveDisabled%}
                    {{ 'CoreHome_DataForThisReportHasBeenDisabled'|translate(externallink('https://matomo.org/faq/how-to-disable-archiving-the-segment-reports-for-specific-plugins'), '</a>')|raw }}
                {% elseif properties.no_data_message %}
                    {{ properties.no_data_message|raw }}
                {% else %}
                    {{ 'CoreHome_ThereIsNoDataForThisReport'|translate }}
                {% endif %}
                </div>
            {% else %}
                {% include visualizationTemplate %}
            {% endif %}

            {% if properties.show_footer %}
                {% include \"@CoreHome/_dataTableFooter.twig\" %}
            {% endif %}

            {% include \"@CoreHome/_dataTableJS.twig\" with { reportId: properties.report_id } %}
        {% endif %}
    </div>
</div>

{% if notifications is not empty and notifications|length %}
    {% for notificationId, n in notifications %}

        {{ n.message|notification({'id': notificationId, 'type': n.type, 'title': n.title, 'noclear': n.hasNoClear, 'context': n.context, 'raw': n.raw}, false) }}

    {% endfor %}
{% endif %}

{% if showCardTableIsEmpty %}
    </div></div>
{% endif %}

{% if showCardAsContentBlock %}
    </div></div>
{% elseif showOnlyTitleWithoutCard %}
    </div>
{% endif %}

{%- endif %}
", "@CoreHome/_dataTable.twig", "/var/www/htdocs/climbing/plugins/CoreHome/templates/_dataTable.twig");
    }
}
