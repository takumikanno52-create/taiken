import sys

#PHPから渡されたパスワードを受け取る
password = sys.argv[1]

#文字の種類を判定する
has_lower = any(c.islower() for c in password)
has_upper = any(c.isupper() for c in password)
has_digit = any(c.isdigit() for c in password)
has_symbol = any(not c.isalnum() for c in password)

#使われている文字の種類数を数える

charset = 0
if has_lower:
    charset += 10
if has_upper:
    charset += 10
if has_digit:
    charset += 10
if has_symbol:
    charset += 32

#文字の種類が判定できなかった場合
if charset ==  0:
    charset = 26

#組み合わせの数を計算する
combinations = charset ** len(password)

#1秒間に１０億回試せると仮定した解析時間を計算する
crack_time = combinations / 1_000_000_000

#結果を出力する
print(crack_time)

