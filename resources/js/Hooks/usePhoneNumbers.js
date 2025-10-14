export const usePhoneNumbers = (phones) => {
    const addPhone = () => {
        phones.value.push({
            number: "",
            dial_code: "",
            type: "oficina",
        });
    };

    const removePhone = (index) => {
        phones.value.splice(index, 1);
    };

    return {
        addPhone,
        removePhone,
    };
};
