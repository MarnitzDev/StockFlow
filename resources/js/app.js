import '../css/app.css';
import './bootstrap';
import 'primeicons/primeicons.css';
import '../css/theme.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// Import PrimeVue
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import { definePreset } from '@primeuix/themes';
import Button from 'primevue/button';
import Calendar from 'primevue/calendar';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Dropdown from 'primevue/dropdown';
import DataTable from 'primevue/datatable';
import DataView from 'primevue/dataview';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';
import Menu from 'primevue/menu';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Stepper from 'primevue/stepper';
import StepList from 'primevue/steplist';
import StepPanels from 'primevue/steppanels';
import StepItem from 'primevue/stepitem';
import StepPanel from 'primevue/steppanel';
import Step from 'primevue/step';
import Textarea from 'primevue/textarea';
import SelectButton from 'primevue/selectbutton';
import TreeTable from 'primevue/treetable';
import Chart from 'primevue/chart';
import Card from 'primevue/card';
import Tag from 'primevue/tag';
import InputSwitch from 'primevue/inputswitch';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';
import ToggleButton from 'primevue/togglebutton';
import Tooltip from 'primevue/tooltip';

import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';

const MyPreset = definePreset(Aura, {
    semantic: {
        primary: {
            50: '{blue.50}',
            100: '{blue.100}',
            200: '{blue.200}',
            300: '{blue.300}',
            400: '{blue.400}',
            500: '{blue.500}',
            600: '{blue.600}',
            700: '{blue.700}',
            800: '{blue.800}',
            900: '{blue.900}',
            950: '{blue.950}'
        }
    }
});

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {
                theme: {
                    preset: MyPreset
                }
            })
            .use(ConfirmationService)
            .use(ToastService);


        app.component('Button', Button);
        app.component('Calendar', Calendar);
        app.component('InputText', InputText);
        app.component('InputNumber', InputNumber);
        app.component('Dropdown', Dropdown);
        app.component('DataTable', DataTable);
        app.component('DataView', DataView);
        app.component('Column', Column);
        app.component('ColumnGroup', ColumnGroup);
        app.component('Row', Row);
        app.component('Menu', Menu);
        app.component('IconField', IconField);
        app.component('InputIcon', InputIcon);
        app.component('Stepper', Stepper);
        app.component('StepPanel', StepPanel);
        app.component('StepList', StepList);
        app.component('StepPanels', StepPanels);
        app.component('StepItem', StepItem);
        app.component('Step', Step);
        app.component('Textarea', Textarea);
        app.component('SelectButton', SelectButton);
        app.component('TreeTable', TreeTable);
        app.component('Chart', Chart);
        app.component('Card', Card);
        app.component('Tag', Tag);
        app.component('InputSwitch', InputSwitch);
        app.component('ConfirmDialog', ConfirmDialog);
        app.component('Toast', Toast);
        app.component('ToggleButton', ToggleButton);
        app.directive('tooltip', Tooltip);

        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
