import { getBreadcrumbs } from "../router";

/**
 * Breadcrumb navigation component for admin pages.
 */
const Breadcrumbs = ({ className = "" }) => {
  const breadcrumbs = getBreadcrumbs();

  if (breadcrumbs.length <= 1) {
    return null;
  }

  return (
    <nav className={`wpm-breadcrumbs ${className}`} aria-label="Breadcrumb">
      <ol className="breadcrumb-list">
        {breadcrumbs.map((crumb, index) => (
          <li key={index} className="breadcrumb-item">
            {crumb.url ? (
              <a href={crumb.url} className="breadcrumb-link">
                {crumb.label}
              </a>
            ) : (
              <span className="breadcrumb-current">{crumb.label}</span>
            )}
            {index < breadcrumbs.length - 1 && (
              <span className="breadcrumb-separator" aria-hidden="true">
                /
              </span>
            )}
          </li>
        ))}
      </ol>
    </nav>
  );
};

export default Breadcrumbs;
