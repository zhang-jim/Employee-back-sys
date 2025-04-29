const Validator = {
    validateAccount(account) {
        if (account.length < 4 || account.length > 20) {
            return { valid: false, message: '帳號長度必須在4到20個字元之間' };
        }
        const hasLowercase = /[A-Za-z]/.test(account);
        const hasNumber = /[0-9]/.test(account);
        if (!hasLowercase || !hasNumber) {
            return { valid: false, message: '帳號必須包含字母和數字' };
        }
        if (!/^[A-Za-z0-9]+$/.test(account)) {
            return { valid: false, message: '帳號只能包含英文和數字，不能有中文、空白或符號' };
        }
        return { valid: true, message: '✅ 帳號格式正確' };
    },

    validatePassword(password) {
        if (password.length < 8 || password.length > 20) {
            return { valid: false, message: '密碼長度必須在8到20個字元之間' };
        }
        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        if (!hasUppercase || !hasLowercase || !hasNumber) {
            return { valid: false, message: '密碼必須包含大寫字母、小寫字母和數字' };
        }
        if (!/^[A-Za-z0-9]+$/.test(password)) {
            return { valid: false, message: '密碼只能包含英文和數字，不能有中文、空白或符號' };
        }
        return { valid: true, message: '✅ 密碼格式正確' };
    },

    validateNickname(nickname) {
        const nicknamePattern = /^[A-Za-z0-9\u4e00-\u9fa5]+$/;

        if (!nickname) {
            return { valid: false, message: '暱稱不能空白' };
        }

        if (nickname.length < 1 || nickname.length > 20) {
            return { valid: false, message: '暱稱長度必須是 1 到 20 個字之間' };
        }

        if (!nicknamePattern.test(nickname)) {
            return { valid: false, message: '暱稱只能包含中英文或數字，不能有特殊符號' };
        }
        return { valid: true, message: '✅ 暱稱格式正確' };
    },

    validatePhonenumber(phonenumber) {
        const phonenumberPattern = /^09[0-9]{8}$/; 

        if (!phonenumberPattern.test(phonenumber)) {
            return { valid: false, message: '錯誤' };
        }
        return { valid: true, message: '✅ 手機號碼格式正確' };
    }
};