export function useCurrencyFormatter() {
    const formatCurrency = (value, locale = 'en-ZA', currency = 'ZAR') => {
        return new Intl.NumberFormat(locale, {
            style: 'currency',
            currency: currency
        }).format(value);
    };

    return {
        formatCurrency
    };
}
