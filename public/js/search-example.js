const searchFormEvent = () => {
    document.getElementById('searchForm').addEventListener(
        'submit',
        async (event) => onShowAllResults(event, true)
    );
}

const showMoreSearchFormEvent = () => {
    document.getElementById('showMoreButton').addEventListener(
        'click',
        async (event) => onShowAllResults(event, false)
    );
}

const searchQuerySetEvent = () => {
    document.getElementById('query').addEventListener(
        'input',
        (event) => onShowAllResults(event, true, true)
    );
}

const clearSearchResults = () => {
    const resultsWrapper = document.getElementById('searchResults');

    if (resultsWrapper) {
        resultsWrapper.innerHTML = '';
    }
}

const beforeSendForm = () => {
    const searchButton = document.getElementById('searchButton');

    if (searchButton) {
        searchButton.disabled = true;
    }

    const showMoreButton = document.getElementById('showMoreButton');

    if (showMoreButton) {
        showMoreButton.disabled = true;
        showMoreButton.remove();
    }
}

const afterSendForm = () => {
    const searchButton = document.getElementById('searchButton');

    if (searchButton) {
        searchButton.disabled = false;
    }

    const showMoreButton = document.getElementById('showMoreButton');

    if (showMoreButton) {
        showMoreButton.disabled = false;
    }
}

const getSearchResults = (data) => {
    return data.map(item => {
        return `
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><a href="${item.url}" target="_blank">${item.title}</a></h5>
                    <p class="card-text">...${item.content}...</p>
                </div>
            </div>
        `;
    });
}
const onError = (response) => {
    console.error('Error fetching search results:', response);
    afterSendForm();
};

const onSuccess = (response) => {
    const resultsWrapper = document.getElementById('searchResults');

    const searchResults = getSearchResults(response.data);

    if (searchResults && searchResults.length > 0) {
        resultsWrapper.innerHTML += searchResults.join('');
    } else {
        resultsWrapper.innerHTML = '';
    }
};

const onShowAllResultsSuccess = (response) => {
    onSuccess(response);

    const currentPage = response.meta.current_page;
    const lastPage = response.meta.last_page;

    if (currentPage && lastPage && currentPage < lastPage) {
        const resultsWrapper = document.getElementById('searchResults');

        resultsWrapper.innerHTML += `
            <button id="showMoreButton" data-next-page="${(currentPage + 1)}" class="btn btn-secondary">Show more</button>
        `;

        showMoreSearchFormEvent();
    }

    afterSendForm();
}

const onSearchQuerySetSuccess = (response) => {
    response.data = response.data.slice(0, 4);

    onSuccess(response);
    afterSendForm();
}

const onShowAllResults = async (event, isFirstPage, isTyping = false) => {
    event.preventDefault();

    const query = document.getElementById('query').value.trim();
    const page = isFirstPage
        ? 1
        : document.getElementById('showMoreButton').getAttribute('data-next-page');
    const data = {
        query: query,
        page: page,
    }

    if (isFirstPage) {
        clearSearchResults();
    }

    beforeSendForm();

    await search(
        'http://localhost',
        data,
        isTyping ? onSearchQuerySetSuccess : onShowAllResultsSuccess,
        onError,
    );
}

searchFormEvent();
searchQuerySetEvent();
