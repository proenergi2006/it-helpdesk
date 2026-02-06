import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

import 'trix';
import 'trix/dist/trix.css';

import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";

window.TomSelect = TomSelect;
window.Swal = Swal;
window.Alpine = Alpine;

Alpine.start();
