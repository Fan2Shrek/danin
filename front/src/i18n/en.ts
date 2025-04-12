import tokens from './tokens.ts'

const translations: { [key: string]: string } = {
    [tokens.login.title]: 'Login',
    [tokens.login.username]: 'Username',
    [tokens.login.password]: 'Password',
    [tokens.login.submit]: 'Login',
    [tokens.login.register.link]: "Don't have an account?",
    [tokens.login.register.cta]: 'Create one',
}

export default translations
