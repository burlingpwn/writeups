# RrEeGgEeXx - Reversing 75

```
State-of-the-art on authentication mechanisms.

Attachment
rev75_79816641bfd11577.zip 
```

Another .NET binary. ILSpy gives us:

```
// RegexAuth.Program
private static void Main(string[] args)
{
	Console.WriteLine("EKO AUTH CHECKER");
	Console.WriteLine("----------------");
	Console.Write("Password: ");
	string input = Console.ReadLine();
	if (Program.check_regex("^.{40}$", input) && Program.check_regex("\\w{3}\\{.*\\}", input) && Program.check_regex("_s.*e_", input) && Program.check_regex("\\{o{2}O{2}o{2}", input) && Program.check_regex("O{2}o{2}O{2}\\}", input) && Program.check_regex("sup3r_r3g3x_challenge", input))
	{
		Console.WriteLine("Welcome master");
		return;
	}
	Console.WriteLine("IMPOSTOR");
}
```

(`check_regex` is just a thin wrapper for the .NET regex stuff.)

So we need to find a string that matches all of these:

```
^.{40}$
\w{3}\{.*\}
_s.*e_
\{o{2}O{2}o{2}
O{2}o{2}O{2}\}
sup3r_r3g3x_challenge
```

After some fiddling, this works:

```
$ mono RegexAuth.exe
EKO AUTH CHECKER
----------------
Password: _sup3r_r3g3x_challenge_{ooOOooOO}aaaaaaa
Welcome master
```

I wrapped it in `EKO{}` and submitted it, but it's not the flag. Obviously we can substitute `aaaaaaa` with 7 characters we like. We can also rearrange some things and still get the "Welcome master" message.  So we'll need to dig deeper.

Look at `\w{3}\{.*\}`. In .NET, `\w` matches any alphanumeric character. So it seems like this regex might be asking us for something more like `EKO\{.*\}`. I then shifted some things around and came up with `EKO{ooOOoo_sup3r_r3g3x_challenge_OOooOO}`, which is 40 characters long. Bingo!
