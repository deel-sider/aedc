<!--
  Matomo - free/libre analytics platform

  @link    https://matomo.org
  @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
-->

<template>
  <div
    class="enrichedHeadline"
    v-on:mouseenter="showIcons = true"
    v-on:mouseleave="showIcons = false"
    ref="root"
  >
    <div
      v-if="!editUrl"
      class="title"
      tabindex="6"
    >
      <slot />
    </div>
    <a
      v-if="editUrl"
      class="title"
      :href="editUrl"
      :title="translate('CoreHome_ClickToEditX', htmlEntities(actualFeatureName))"
    >
      <slot />
    </a>
    <span
      v-show="showIcons || showInlineHelp"
      class="iconsBar"
    >
      <a
        v-if="helpUrl && !actualInlineHelp"
        rel="noreferrer noopener"
        target="_blank"
        class="helpIcon"
        :href="helpUrl"
        :title="translate('CoreHome_ExternalHelp')"
      ><span class="icon-help" /></a>
      <a
        v-if="actualInlineHelp"
        v-on:click="showInlineHelp = !showInlineHelp"
        class="helpIcon"
        :class="{ 'active': showInlineHelp }"
        :title="translate(reportGenerated ? 'General_HelpReport' : 'General_Help')"
      ><span class="icon-info" /></a>
      <div class="ratingIcons" v-if="showRateFeature">
        <component :title="actualFeatureName" :is="rateFeature"></component>
      </div>
    </span>
    <div
      class="inlineHelp"
      v-show="showInlineHelp"
    >
      <div v-html="$sanitize(actualInlineHelp)"/>
      <span class="helpDate"
            v-if="reportGenerated!=''"
            v-html="$sanitize(reportGenerated)"></span>
      <a
        v-if="helpUrl"
        rel="noreferrer noopener"
        target="_blank"
        class="readMore"
        :href="helpUrl"
      >{{ translate('General_MoreDetails') }}</a>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import Matomo from '../Matomo/Matomo';
import Periods from '../Periods/Periods';
import { translateOrDefault } from '../translate';
import useExternalPluginComponent from '../useExternalPluginComponent';

interface EnrichedHeadlineData {
  showIcons: boolean;
  showInlineHelp: boolean;
  actualFeatureName?: string | null;
  actualInlineHelp?: string | null,
}

/**
 * Usage:
 *
 * <h2><EnrichedHeadline>All Websites Dashboard</EnrichedHeadline></h2>
 * -> uses "All Websites Dashboard" as featurename
 *
 * <h2><EnrichedHeadline feature-name="All Websites Dashboard">All Websites Dashboard (Total:
 * 309 Visits)</EnrichedHeadline></h2>
 * -> custom featurename
 *
 * <h2><EnrichedHeadline help-url="https://matomo.org/guide">All Websites Dashboard</EnrichedHeadline></h2>
 * -> shows help icon and links to external url
 *
 * <h2><EnrichedHeadline edit-url="index.php?module=Foo&action=bar&id=4">All Websites
 * Dashboard</EnrichedHeadline></h2>
 * -> makes the headline clickable linking to the specified url
 *
 * <h2><EnrichedHeadline inline-help="inlineHelp">Pages report</EnrichedHeadline></h2>
 * -> inlineHelp specified via a attribute shows help icon on headline hover
 *
 * <h2><EnrichedHeadline>All Websites Dashboard
 *     <div class="inlineHelp">My <strong>inline help</strong></div>
 * </EnrichedHeadline></h2>
 * -> alternative definition for inline help
 * -> shows help icon to display inline help on click. Note: You can combine inlinehelp and help-url
 *
 * * <h2><EnrichedHeadline report-generated="generated time">Pages report</EnrichedHeadline></h2>
 * -> reportGenerated specified via this attribute shows a clock icon with a tooltip which
 * activated by hover
 * -> the tooltip shows the value of the attribute
 */
export default defineComponent({
  props: {
    helpUrl: {
      type: String,
      default: '',
    },
    editUrl: {
      type: String,
      default: '',
    },
    reportGenerated: String,
    featureName: String,
    inlineHelp: String,
  },
  data(): EnrichedHeadlineData {
    return {
      showIcons: false,
      showInlineHelp: false,
      actualFeatureName: this.featureName,
      actualInlineHelp: this.inlineHelp,
    };
  },
  watch: {
    inlineHelp(newValue: string) {
      this.actualInlineHelp = newValue;
    },
    featureName(newValue: string) {
      this.actualFeatureName = newValue;
    },
  },
  mounted() {
    const root = this.$refs.root as HTMLElement;

    if (!this.actualInlineHelp) {
      let helpNode = root.querySelector('.title .inlineHelp');
      if (!helpNode && root.parentElement?.nextElementSibling) {
        // hack for reports :(
        helpNode = (root.parentElement.nextElementSibling as HTMLElement)
          .querySelector('.reportDocumentation');
      }

      if (helpNode) {
        // hackish solution to get binded html of p tag within the help node
        // at this point the ng-bind-html is not yet converted into html when report is not
        // initially loaded. Using $compile doesn't work. So get and set it manually
        const helpDocs = helpNode.getAttribute('data-content')?.trim();
        if (helpDocs && helpDocs.length) {
          this.actualInlineHelp = `<p>${helpDocs}</p>`;
          setTimeout(() => helpNode!.remove(), 0);
        }
      }
    }

    if (!this.actualFeatureName) {
      this.actualFeatureName = root.querySelector('.title')?.textContent;
    }

    if (Matomo.period && Matomo.currentDateString) {
      const currentPeriod = Periods.parse(
        Matomo.period as string,
        Matomo.currentDateString as string,
      );

      if (this.reportGenerated
        && currentPeriod.containsToday()
      ) {
        window.$(root.querySelector('.report-generated')!).tooltip({
          track: true,
          content: this.reportGenerated,
          items: 'div',
          show: false,
          hide: false,
        });
      }
    }
  },
  methods: {
    htmlEntities(v: string) {
      return Matomo.helper.htmlEntities(v);
    },
  },
  computed: {
    showRateFeature() {
      return translateOrDefault('Feedback_SendFeedback') !== 'Feedback_SendFeedback';
    },
    rateFeature() {
      if (this.showRateFeature) {
        return useExternalPluginComponent('Feedback', 'RateFeature');
      }
      return '';
    },
  },
});
</script>
