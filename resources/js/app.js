import './bootstrap';

import './libs/alpine';
import Alpine from 'alpinejs';
import persist from '@alpinejs/persist'
import collapse from '@alpinejs/collapse'
import focus from '@alpinejs/focus'
import FormsAlpinePlugin from '../../vendor/filament/forms/dist/module.esm'
import NotificationsAlpinePlugin from '../../vendor/filament/notifications/dist/module.esm'
import Tooltip from "@ryangjchandler/alpine-tooltip";
import Chart from 'chart.js/auto';
import './elements/turbo-echo-stream-tag';
import './libs/turbo';

Alpine.plugin(collapse)
Alpine.plugin(persist)
Alpine.plugin(focus)
Alpine.plugin(FormsAlpinePlugin)
Alpine.plugin(NotificationsAlpinePlugin)
Alpine.plugin(Tooltip)
window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();
