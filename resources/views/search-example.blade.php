<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search example</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .search-results {
            margin-top: 20px;
        }

        .pagination {
            justify-content: center;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1>Search example</h1>
    <div class="row mt-4">
        <div class="col-md-6">
            <form id="searchForm">
                <div class="input-group mb-3">
                    <input type="text"
                           id="query"
                           name="query"
                           class="form-control"
                           placeholder="Enter search query"
                           required
                    >
                    <div class="input-group-append">
                        <button id="searchButton" class="btn btn-primary" type="submit">Show all results</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="search-results" id="searchResults"></div>
    <br>
</div>
<script src="{{ asset('js/search.js') }}"></script>
<script src="{{ asset('js/search-example.js') }}"></script>
</body>
</html>
