import '../css/app.css';
import './bootstrap';
import 'primeicons/primeicons.css';
import '../css/theme.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

// Import PrimeVue
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import { definePreset } from '@primeuix/themes';

// Import PrimeVue components
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
import Dialog from 'primevue/dialog';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';
import ToggleButton from 'primevue/togglebutton';
import Rating from 'primevue/rating';
import Divider from 'primevue/divider';
import Select from 'primevue/select';
import Breadcrumb from 'primevue/breadcrumb';
import Paginator from 'primevue/paginator';
import MegaMenu from 'primevue/megamenu';
import MultiSelect from 'primevue/multiselect';
import OrganizationChart from 'primevue/organizationchart';
import Chip from 'primevue/chip';
import Checkbox from 'primevue/checkbox';
import FileUpload from 'primevue/fileupload';
import Skeleton from 'primevue/skeleton';

import Tooltip from 'primevue/tooltip';

import DialogService from 'primevue/dialogservice';
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
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {
                theme: {
                    preset: MyPreset
                }
            })
            .use(DialogService)
            .use(ConfirmationService)
            .use(ToastService);

        // Register PrimeVue components
        [
            Button, Calendar, InputText, InputNumber, Dropdown, DataTable, DataView,
            Column, ColumnGroup, Row, Menu, IconField, InputIcon, Stepper, StepList,
            StepPanels, StepItem, StepPanel, Step, Textarea, SelectButton, TreeTable,
            Chart, Card, Tag, InputSwitch, Dialog, ConfirmDialog, Toast, ToggleButton,
            Rating, Divider, Select, Breadcrumb, Paginator, MegaMenu, MultiSelect,
            OrganizationChart, Chip, Checkbox, FileUpload, Skeleton
        ].forEach(component => {
            app.component(component.name, component);
        });

        app.directive('tooltip', Tooltip);

        return app.mount(el);
    },
    progress: {
        color: '#29d',
        showSpinner: true,
        includeCSS: false,
    }
});
