#include <stdlib.h>
#include <pwd.h>
#include <stdio.h>
#include <unistd.h>

/*
SEC335 Illustrate SUID Programs
* based on: https://stackoverflow.com/questions/8953424/how-to-get-the-username-in-c-c-in-linux
* Make sure run the following
* sudo chown root:root nameofprogram
* sudo chmod u+s nameofprogram
*/

int main(int argc, char *argv[])
{
  struct passwd *pw;
  uid_t uid;
 
  uid = geteuid ();
  pw = getpwuid (uid);
  if (pw)
    {
      puts (pw->pw_name);
      exit (EXIT_SUCCESS);
    }
  else
  {
     puts ("Error");
     exit (EXIT_FAILURE);
  }
}
