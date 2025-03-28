import Plugin from './components/annotationsTabPlugin.vue';
import {AnnotationsTabPlugins} from './import.js';

if (AnnotationsTabPlugins) {
    AnnotationsTabPlugins.assistanceRequest = Plugin;
}
