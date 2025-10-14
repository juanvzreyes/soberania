export const gradientBgBase = "bg-gradient-to-tr";
export const gradientBgPurplePink = `${gradientBgBase} from-purple-400 via-pink-500 to-red-500`;
export const gradientBgDark = `${gradientBgBase} from-sky-900 via-blue-950 to-sky-950`;
export const gradientBgPinkRed = `${gradientBgBase} from-pink-400 via-red-500 to-yellow-500`;
export const gradientBgWhite = `${gradientBgBase} from-white via-sky-200 to-gray-50`;
export const gradientBgLight = `${gradientBgBase} from-white via-gray-50 to-blue-100`;

export const colorsBg = {
  white: "bg-white text-black",
  light: "bg-white text-black dark:bg-slate-900/70 dark:text-white",
  contrast: "bg-gray-800 text-white dark:bg-white dark:text-black",
  success: "bg-emerald-500 border-emerald-500 text-white",
  danger: "bg-red-500 border-red-800 text-white",
  warning: "bg-yellow-500 border-yellow-500 text-white",
  info: "bg-forest-400 border-forest-800 text-white",
};

export const colorsBgLight = {
  white: "bg-white text-black",
  light: "bg-white text-forest-400 dark:bg-slate-900/70 dark:text-white border-gray-600",
  contrast: "bg-gray-800 text-white dark:bg-white dark:text-black border-gray-900",
  success: "bg-emerald-100 text-emerald-500 border-emerald-600",
  danger: "bg-red-100 text-red-500 border-red-600",
  warning: "bg-yellow-100 text-yellow-500 border-yellow-600",
  info: "bg-blue-100 text-blue-500 border-blue-600",
};

export const colorsText = {
  white: "text-black dark:text-slate-100",
  light: "text-gray-700 dark:text-slate-400",
  contrast: "dark:text-white",
  success: "text-emerald-500",
  danger: "text-red-500",
  warning: "text-yellow-500",
  info: "text-blue-500",
};

export const colorsOutline = {
  white: [colorsText.white, "border-gray-100"],
  light: [colorsText.light, "border-gray-100"],
  contrast: [colorsText.contrast, "border-gray-900 dark:border-slate-100"],
  success: [colorsText.success, "border-emerald-500"],
  danger: [colorsText.danger, "border-red-500"],
  warning: [colorsText.warning, "border-yellow-500"],
  info: [colorsText.info, "border-blue-500"],
};

export const getButtonColor = (
  color,
  isOutlined,
  hasHover,
  isActive = false
) => {
  const colors = {
    ring: {
      white: "ring-gray-200 dark:ring-gray-500",
      whiteDark: "ring-gray-200 dark:ring-gray-500",
      lightDark: "ring-gray-200 dark:ring-gray-500",
      contrast: "ring-gray-300 dark:ring-gray-400",
      success: "ring-success-400 dark:ring-emerald-700",
      danger: "ring-red-300 dark:ring-red-700",
      warning: "ring-warning-300 dark:ring-yellow-700",
      info: "ring-forest-100 dark:ring-blue-700",
    },
    active: {
      white: "bg-gray-100",
      whiteDark: "bg-gray-100 dark:bg-slate-800",
      lightDark: "bg-gray-200 dark:bg-slate-700",
      contrast: "bg-gray-700 dark:bg-slate-100",
      success: "bg-success-400 dark:bg-emerald-600",
      danger: "bg-red-700 dark:bg-red-600",
      warning: "bg-warning-400 dark:bg-yellow-600",
      info: "bg-forest-200 dark:bg-blue-600",
    },
    bg: {
      white: "bg-white text-black",
      whiteDark: "bg-white text-black dark:bg-slate-900 dark:text-white",
      lightDark: "bg-transparent text-black dark:bg-slate-800 dark:text-white",
      contrast: "bg-gray-800 text-white dark:bg-white dark:text-black",
      success: "bg-success-400 dark:bg-emerald-500 text-white",
      danger: "bg-error-400 dark:bg-red-500 text-white",
      warning: "bg-warning-300 dark:bg-yellow-500 text-white",
      info: "bg-forest-400 dark:bg-blue-500 text-white",
    },
    bgHover: {
      white: "hover:bg-gray-100",
      whiteDark: "hover:bg-gray-100 hover:dark:bg-slate-800",
      lightDark: "hover:bg-forest-100 hover:text-white hover:dark:bg-slate-700",
      contrast: "hover:bg-gray-700 hover:dark:bg-slate-100",
      success: "hover:bg-emerald-800 hover:border-emerald-800 hover:dark:bg-emerald-600 hover:dark:border-emerald-600",
      danger: "hover:bg-red-800 hover:border-red-800 hover:dark:bg-red-600 hover:dark:border-red-600",
      warning: "hover:bg-warning-400 hover:border-warning-400 hover:dark:bg-yellow-600 hover:dark:border-yellow-600",
      info: "hover:bg-forest-400/85 hover:border-forest-400 hover:dark:bg-blue-600 hover:dark:border-blue-600",
    },
    borders: {
      white: "border-white",
      whiteDark: "border-white dark:border-slate-900",
      lightDark: "border-gray-300 dark:border-slate-600",
      contrast: "border-gray-800 dark:border-white",
      success: "border-success-400 dark:border-emerald-500",
      danger: "border-red-600 dark:border-red-500",
      warning: "border-warning-300 dark:border-yellow-500",
      info: "border-forest-100 dark:border-blue-500",
    },
    text: {
      contrast: "dark:text-slate-100",
      success: "text-success-300 dark:text-emerald-500",
      danger: "text-red-600 dark:text-red-500",
      warning: "text-warning-300 dark:text-yellow-500",
      info: "text-forest-100 dark:text-blue-500",
    },
    outlineHover: {
      contrast: "hover:bg-gray-800 hover:text-gray-100 hover:dark:bg-slate-100 hover:dark:text-black",
      success: "hover:bg-success-300 hover:text-white hover:dark:text-white hover:dark:border-emerald-600",
      danger: "hover:bg-red-600 hover:text-white hover:dark:text-white hover:dark:border-red-600",
      warning: "hover:bg-warning-300 hover:text-white hover:dark:text-white hover:dark:border-yellow-600",
      info: "hover:bg-forest-100 hover:text-white hover:dark:text-white hover:dark:border-blue-600",
    },
  };

  if (!colors.bg[color]) {
    return color;
  }

  const isOutlinedProcessed =
    isOutlined && ["white", "whiteDark", "lightDark"].indexOf(color) < 0;

  const base = [colors.borders[color], colors.ring[color]];

  if (isActive) {
    base.push(colors.active[color]);
  } else {
    base.push(isOutlinedProcessed ? colors.text[color] : colors.bg[color]);
  }

  if (hasHover) {
    base.push(
      isOutlinedProcessed ? colors.outlineHover[color] : colors.bgHover[color]
    );
  }

  return base;
};
