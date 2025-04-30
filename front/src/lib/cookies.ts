// todo add expires
export const setCookie = (name: string, value: string) => {
    document.cookie = `${name}=${encodeURIComponent(value)};`;
};

export const getCookie = (name: string): string | null => {
    const cookies = document.cookie.split('; ');
    const value = cookies.find((cookie) => cookie.startsWith(`${name}=`))?.split('=')[1];

    return value ? decodeURIComponent(value) : null;
};
