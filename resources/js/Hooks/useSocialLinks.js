export const useSocialLinks = (socialNetworks) => {
    const addSocialLink = () => {
        socialNetworks.value.push({
            url: "",
            type: null,
        });
    };

    const removeSocialLink = (index) => {
        socialNetworks.value.splice(index, 1);
    };

    return {
        addSocialLink,
        removeSocialLink,
    };
};
