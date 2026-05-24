<?php
/**
 * Captcha Helper for simple math verification
 * 
 * This file contains the helper class to handle generation, rendering, 
 * and session-based validation of mathematical captchas to prevent bot spam.
 */
class CaptchaHelper {
    /**
     * Generate a random math question and store the answer in session.
     * Returns the question text.
     */
    public static function generate() {
        $operators = ['+', '-'];
        $op = $operators[array_rand($operators)];
        
        if ($op === '+') {
            // Addition: Random numbers between 1 and 15
            $num1 = rand(1, 15);
            $num2 = rand(1, 15);
            $question = "$num1 + $num2";
            $answer = $num1 + $num2;
        } else {
            // Subtraction: Num1 is between 10 and 20, Num2 is between 1 and 9
            // This ensures the result is always positive and simple to calculate
            $num1 = rand(10, 20);
            $num2 = rand(1, 9);
            $question = "$num1 - $num2";
            $answer = $num1 - $num2;
        }
        
        $_SESSION['captcha_question'] = $question;
        $_SESSION['captcha_answer'] = $answer;
        
        return $question;
    }

    /**
     * Get the current question or generate one if not set
     */
    public static function getQuestion() {
        if (empty($_SESSION['captcha_question'])) {
            return self::generate();
        }
        return $_SESSION['captcha_question'];
    }

    /**
     * Validate the user's answer
     */
    public static function validate($userAnswer) {
        if (!isset($_SESSION['captcha_answer'])) {
            return false;
        }
        
        $isValid = (int)trim($userAnswer) === (int)$_SESSION['captcha_answer'];
        
        // Always generate a new captcha after validation attempt to prevent replay attacks
        self::generate();
        
        return $isValid;
    }
}
