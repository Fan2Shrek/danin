import tokens from './tokens.ts';

const translations: { [key: string]: string } = {
    [tokens.login.title]: 'Login',
    [tokens.login.email]: 'Email',
    [tokens.login.password]: 'Password',
    [tokens.login.submit]: 'Login',
    [tokens.login.register.link]: "Don't have an account?",
    [tokens.login.register.cta]: 'Create one',
    [tokens.login.error.invalid]: 'Invalid username or password',
    [tokens.login.error.empty]: 'Please fill all fields',
    [tokens.footer.copyright]: 'All rights reserved',
    [tokens.footer.links.games.title]: 'Games',
    [tokens.footer.links.games.supportedList]: 'Supported games',
    [tokens.footer.links.games.suggestion]: 'Suggest a game',
    [tokens.footer.links.documentation.title]: 'Documentation',
    [tokens.footer.links.documentation.createRoom]: 'Create a room',
    [tokens.footer.links.documentation.connectGame]: 'Connect a game',
    [tokens.footer.links.documentation.connectTchat]: 'Connect a chat',
    [tokens.footer.links.currentGames.title]: 'Current rooms',
    [tokens.footer.support.title]: 'Support us',
};

export default translations;
