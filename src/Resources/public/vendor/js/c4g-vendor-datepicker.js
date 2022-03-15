import Datepicker from 'vanillajs-datepicker/Datepicker';
// import DateRangePicker from 'vanillajs-datepicker/DateRangePicker';
import de from 'vanillajs-datepicker/locales/de';
Object.assign(Datepicker.locales, de);
// Object.assign(DateRangePicker.locales, de);

window.Datepicker = Datepicker;
// window.DateRangePicker = DateRangePicker;