export function FormField({
  autoComplete,
  label,
  name,
  onChange,
  placeholder,
  type = 'text',
  value,
}) {
  return (
    <label className="field">
      <span>{label}</span>
      <input
        autoComplete={autoComplete}
        className="input"
        name={name}
        onChange={onChange}
        placeholder={placeholder}
        type={type}
        value={value}
      />
    </label>
  );
}
