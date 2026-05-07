export function Panel({ children, className = '', title, description, actions }) {
  return (
    <section className={`panel ${className}`.trim()}>
      {title ? (
        <div className="panel-head">
          <div>
            <h3>{title}</h3>
            {description ? <p className="supporting-copy">{description}</p> : null}
          </div>
          {actions ? <div className="page-actions">{actions}</div> : null}
        </div>
      ) : null}
      {children}
    </section>
  );
}
