<script setup>
import DatePicker from 'vue3-persian-datetime-picker';
import { ref, watch } from 'vue';

const props = defineProps({
    modelValue: String, // Accepts Gregorian date string 'YYYY-MM-DD'
});

const emit = defineEmits(['update:modelValue']);

// This will hold the Jalali date for display purposes
const jalaliDate = ref('');

// Helper to convert Gregorian YYYY-MM-DD to Jalali
const toJalali = (gregorianDate) => {
    if (!gregorianDate) return '';
    const [year, month, day] = gregorianDate.split('-').map(Number);
    const j = new Date(year, month - 1, day).toLocaleDateString('fa-IR-u-nu-latn').split('/');
    // Pad months and days with a leading zero if needed
    const jMonth = j[1].padStart(2, '0');
    const jDay = j[2].padStart(2, '0');
    return `${j[0]}/${jMonth}/${jDay}`;
};

// Helper to convert Jalali YYYY/MM/DD to Gregorian
const toGregorian = (jalaliDate) => {
    if (!jalaliDate) return null;
    const [jYear, jMonth, jDay] = jalaliDate.split('/').map(Number);
    // A bit of a hack to get the Gregorian date from Jalali parts
    const g = new Date(0);
    g.setFullYear(jYear - 621, jMonth - 1, jDay + 1); // Approximate conversion
    // More accurate conversion might be needed, but this works for many cases.
    // Let's use a more stable library for conversion if needed. For now, we trust the component.
    // The component itself emits Gregorian, which is better.
    return null; // We will handle the conversion from the component's output directly.
};


// When the component's modelValue (Gregorian date from form) changes, update the displayed Jalali date
watch(() => props.modelValue, (newVal) => {
    jalaliDate.value = toJalali(newVal);
}, { immediate: true });

// When the user picks a date, the component emits the new date in Gregorian format.
// We just need to forward this to the parent form.
const handleDateChange = (gregorianDate) => {
    emit('update:modelValue', gregorianDate);
};
</script>

<template>
    <date-picker
        :modelValue="modelValue"
        @update:modelValue="handleDateChange"
        format="YYYY-MM-DD"
        display-format="jYYYY/jMM/jDD"
        placeholder="تاریخ را انتخاب کنید"
        class="jalali-date-picker"
    />
</template>

<style>
/* Optional: Custom styling for the date picker input to match the project's theme */
.jalali-date-picker input {
    width: 100%;
    border-radius: 0.375rem;
    border: 1px solid #d1d5db;
    padding: 0.5rem 0.75rem;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
.jalali-date-picker input:focus {
    border-color: #4f46e5;
    outline: none;
    box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.5);
}
</style>
