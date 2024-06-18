const search = async (appUrl, requestData, onSuccess, onError = null) => {
    const query = `query=${requestData.query}&page=${requestData.page}`;
    const response = await fetch(`${appUrl}/api/pages/search?${query}`);
    const responseData = await response.json();

    if (response.status === 200) {
        onSuccess(responseData);
    } else {
        if (onError) {
            onError(responseData);
        }
    }
}
