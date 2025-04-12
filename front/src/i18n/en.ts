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
};

export default translations;
