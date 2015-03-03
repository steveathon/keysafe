phpKeySafe
=======

First written in 2012, this class is designed as a demonstration of one method of
keeping your secure keys, client IDs, passwords and other such things out of your
source code and seperated. 

One of the challenges in working with code is making sure to store your passwords
securely. This little helper is designed so that you can store your passwords in a
simple json file away from your main application directory, thus enabling you to
commit your code without checking for passwords first.

Very beta, this idea is not new nad there's a lot of people doing similar projects,
but this is a simple example you can use to use to your liking. One thing I would
say of this class is that it provides sufficient seperation of secure items from
your source control but doesn't provide security from any bad code.

Enjoy at your own risk.