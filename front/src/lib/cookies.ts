// todo add expires
export const setCookie = (name: string, value: string, expire: Date|null = null) => {
    document.cookie = `${name}=${encodeURIComponent(value)}; path=/; ${expire ? `expires=${expire.toUTCString()};`: ''}`;
};

export const getCookie = (name: string): string | null => {
    const cookies = document.cookie.split('; ');
    const value = cookies.find((cookie) => cookie.startsWith(`${name}=`))?.split('=')[1];

    return value ? decodeURIComponent(value) : null;
};

export const deleteCookie = (name: string) => {
    document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
};
