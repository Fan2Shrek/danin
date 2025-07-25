import tokens from './tokens.ts';

const translations: { [key: string]: string } = {
    [tokens.login.title]: 'Login',
    [tokens.login.email]: 'Email',
    [tokens.login.password]: 'Password',
    [tokens.login.submit]: 'Login',
    [tokens.login.register.link]: "Don't have an account?",
    [tokens.login.register.cta]: 'Create one',
    [tokens.login.totp.title]: 'Two-Factor Authentication',
    [tokens.login.totp.submit]: 'Verify',
    [tokens.login.totp.input]: 'Enter your authentication code',
    [tokens.login.totp.error]: 'The authentication code is invalid or expired',
    [tokens.login.error.invalid]: 'Invalid username or password',
    [tokens.login.error.empty]: 'Please fill all fields',
    [tokens.home.title]: 'Welcome to Danin',
    [tokens.home.subtitle]: 'Discover a new way to play with your friends',
    [tokens.home.cta]: 'Play now',
    [tokens.home.games.title]: 'Our games',
    [tokens.home.providers.title]: 'Chats',
    [tokens.home.events.title]: 'Upcoming events',
    [tokens.user.username]: 'Username',
    [tokens.user.email]: 'Email',
    [tokens.user.password]: 'Password',
    [tokens.user.constraints.password.minLength]: 'Password must be at least 8 characters long',
    [tokens.user.constraints.password.uppercase]: 'Add at least one uppercase letter',
    [tokens.user.constraints.password.lowercase]: 'Add at least one lowercase letter',
    [tokens.user.constraints.password.number]: 'Add at least one number',
    [tokens.user.constraints.password.special]: 'Add at least one special character',
    [tokens.user.constraints.password.strength.veryWeak]: 'Very weak',
    [tokens.user.constraints.password.strength.weak]: 'Weak',
    [tokens.user.constraints.password.strength.medium]: 'Medium',
    [tokens.user.constraints.password.strength.strong]: 'Strong',
    [tokens.user.constraints.password.strength.excellent]: 'Excellent',
    [tokens.room.create.game.title]: 'Create a room',
    [tokens.room.create.game.howTo]: 'How to setup {gameName} for danin',
    [tokens.room.create.game.cta]: 'Click here to see',
    [tokens.room.create.settings.title]: 'Danin settings',
    [tokens.room.create.settings.transport]: 'Transport',
    [tokens.room.create.settings.host]: 'IP Address',
    [tokens.room.create.settings.port]: 'Port',
    [tokens.room.create.commands.title]: 'Commands',
    [tokens.room.create.providers.title]: 'Chat',
    [tokens.room.create.submit]: 'Create room',
    [tokens.room.start.title]: 'Start room',
    [tokens.room.start.description]: 'Waiting the game to connect',
    [tokens.room.start.error]: 'An error occurred while starting the room',
    [tokens.room.start.requestConnectionToGame]: 'Request connection to the game',
    [tokens.room.start.localSetupError]: 'An error occurred while setting up the room locally',
    [tokens.room.start.providers]: 'Enable chats',
    [tokens.room.start.providerCommand]: 'To enable it, enter the following command in the chat',
    [tokens.room.start.copyToken]: 'Copy token',
    [tokens.room.start.go]: 'Go to chat',
    [tokens.totp.enable]: 'Enable Two-Factor Authentication',
    [tokens.totp.description]: 'Scan the QR code with your authenticator app',
    [tokens.update.title]: 'Update Profile',
    [tokens.update.subtitle]: 'Update your personal information',
    [tokens.update.submit]: 'Update Profile',
    [tokens.update.success]: 'Profile updated successfully',
    [tokens.register.title]: 'Create Account',
    [tokens.register.subtitle]: 'Join us by creating your account',
    [tokens.register.success]: 'Registration successful! Welcome!',
    [tokens.register.confirmPassword]: 'Confirm Password',
    [tokens.register.submit]: 'Create Account',
    [tokens.register.submitting]: 'Creating...',
    [tokens.register.placeholder.username]: 'Enter your username',
    [tokens.register.placeholder.email]: 'Enter your email address',
    [tokens.register.placeholder.password]: 'Enter your password',
    [tokens.register.placeholder.confirmPassword]: 'Confirm your password',
    [tokens.register.error.miscellaneous]:
        'An error occurred during registration. Please try again.',
    [tokens.register.error.username.alreadyExists]:
        'Username already exists. Please choose another one.',
    [tokens.register.error.username.minLength]: 'Username must be at least 3 characters long',
    [tokens.register.error.username.maxLength]: 'Username must be less than 20 characters',
    [tokens.register.error.username.regex]:
        'Username can only contain letters, numbers, and underscores',
    [tokens.register.error.email.invalid]: 'Email address is invalid',
    [tokens.register.error.email.alreadyExists]:
        'This email address is already in use. Please choose another one.',
    [tokens.register.error.password.minLength]: 'Password must be at least 8 characters long',
    [tokens.register.error.password.notStrong]: 'Please choose a stronger password',
    [tokens.register.error.confirmPassword.mismatch]: 'Passwords do not match',
    [tokens.games.title]: 'Available Games',
    [tokens.games.subtitle]: 'Explore our selection of games and join a match',
    [tokens.room.tchat.title]: 'Tchat',
    [tokens.room.tchat.send]: 'Send message',
    [tokens.room.tchat.placeholder]: 'Write a message...',
    [tokens.room.tchat.commands.title]: 'Command list',
    [tokens.room.tchat.commands.description]: 'Here is the list of available commands:',
    [tokens.navbar.links.games]: 'Games',
    [tokens.navbar.links.tchat]: 'Chat',
    [tokens.navbar.links.createRoom]: 'Create a room',
    [tokens.navbar.links.login]: 'Login',
    [tokens.navbar.links.logout]: 'Logout',
    [tokens.navbar.links.profile]: 'My profile',
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
