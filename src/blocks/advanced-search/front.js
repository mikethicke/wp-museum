import { useState, useEffect, createRoot } from "@wordpress/element";

import apiFetch from "@wordpress/api-fetch";

import { AdvancedSearchUI } from "../../components";

import { PaginatedObjectList } from "../../components/object-list/object-list";

window.addEventListener("DOMContentLoaded", () => {
  const advancedSearchElements = document.getElementsByClassName(
    "wpm-advanced-search-block-frontend"
  );
  if (!!advancedSearchElements) {
    for (let i = 0; i < advancedSearchElements.length; i++) {
      const advancedSearchElement = advancedSearchElements[i];
      const attribuesJSON = advancedSearchElement.dataset.attributes;
      const attributes = JSON.parse(attribuesJSON ? attribuesJSON : "{}");
      if (typeof attributes["defaultSearch"] != "string") {
        attributes["defaultSearch"] = JSON.stringify(
          attributes["defaultSearch"]
        );
      }
      const root = createRoot(advancedSearchElement);
      root.render(<AdvancedSearchFront attributes={attributes} />);
    }
  }
});

const AdvancedSearchFront = (props) => {
  const { attributes } = props;

  const {
    defaultSearch,
    fixSearch,
    runOnLoad,
    showObjectType,
    showTitleToggle,
    showFlags,
    showCollections,
    showFields,
    resultsPerPage,
  } = attributes;

  const [collectionData, setCollectionData] = useState({});
  const [kindsData, setKindsData] = useState([]);
  const [searchResults, setSearchResults] = useState([]);
  const [currentSearchParams, setCurrentSearchParams] = useState([]);

  const baseRestPath = "/wp-museum/v1";

  useEffect(() => {
    updateCollectionData();
    updateKindsData();

    if (runOnLoad && defaultSearch) {
      onSearch(JSON.parse(defaultSearch));
    }
  }, []);

  const updateCollectionData = () => {
    apiFetch({ path: `${baseRestPath}/collections` }).then((result) =>
      setCollectionData(result)
    );
  };

  const updateKindsData = () => {
    apiFetch({ path: `${baseRestPath}/mobject_kinds` }).then((result) =>
      setKindsData(result)
    );
  };

  const getFieldData = (postType) => {
    return apiFetch({ path: `${baseRestPath}/${postType}/fields` });
  };

  const onSearch = (searchParams) => {
    for (const [key, value] of Object.entries(searchParams)) {
      if (key !== "page" && value !== currentSearchParams[key]) {
        searchParams.page = 1;
        break;
      }
    }
    searchParams.posts_per_page = resultsPerPage;
    setCurrentSearchParams(searchParams);
    apiFetch({
      path: `${baseRestPath}/search`,
      method: "POST",
      data: searchParams,
      parse: false,
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then((data) => {
        setSearchResults(data);
      })
      .catch((error) => {
        console.error("Search request failed:", error);
        setSearchResults([]); // Reset to empty state on error
      });
  };

  let currentPage = 1;
  let totalPages = 0;
  if (
    searchResults.length > 0 &&
    typeof searchResults[0].query_data !== "undefined"
  ) {
    currentPage = searchResults[0].query_data.current_page;
    totalPages = searchResults[0].query_data.num_pages;
  }

  return (
    <>
      {!fixSearch && (
        <AdvancedSearchUI
          defaultSearch={defaultSearch}
          showFlags={showFlags}
          showCollections={showCollections}
          showFields={showFields}
          showObjectType={showObjectType}
          showTitleToggle={showTitleToggle}
          collectionData={collectionData}
          kindsData={kindsData}
          getFieldData={getFieldData}
          inEditor={false}
          onSearch={onSearch}
        />
      )}
      {searchResults && (
        <PaginatedObjectList
          currentPage={currentPage}
          totalPages={totalPages}
          searchCallback={onSearch}
          searchParams={currentSearchParams}
          mObjects={searchResults}
          displayImages={true}
        />
      )}
    </>
  );
};

export default AdvancedSearchFront;
