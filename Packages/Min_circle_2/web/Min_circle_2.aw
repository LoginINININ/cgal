@! ============================================================================
@! The CGAL Project
@! Implementation: 2D Smallest Enclosing Circle
@! ----------------------------------------------------------------------------
@! file  : web/Optimisation/Min_circle_2.aw
@! author: Bernd G�rtner, Sven Sch�nherr (sven@inf.fu-berlin.de)
@! ----------------------------------------------------------------------------
@! $Revision$
@! $Date$
@! ============================================================================
 
@p maximum_input_line_length = 180
@p maximum_output_line_length = 180

@documentclass[twoside]{article}
@usepackage[latin1]{inputenc}
@usepackage{a4wide2}
@usepackage{amssymb}
@usepackage{cc_manual}
@article

\input{cprog.sty}
\setlength{\parskip}{1ex}

@! LaTeX macros
\newenvironment{pseudocode}[1]%
  {\vspace*{-0.5\baselineskip} \upshape \begin{tabbing}
     99 \= bla \= bla \= bla \= bla \= bla \= bla \= bla \= bla \kill
     #1 \+ \\}%
  {\end{tabbing}}
\newcommand{\keyword}[1]{\texttt{#1}}
\newcommand{\IF}{\keyword{IF} }
\newcommand{\THEN}{\keyword{THEN} \+ \\}
\newcommand{\ELSE}{\< \keyword{ELSE} \\}
\newcommand{\END}{\< \keyword{END} \- \\ }
\newcommand{\OR}{\keyword{OR} }
\newcommand{\FOR}{\keyword{FOR} }
\newcommand{\TO}{\keyword{TO} }
\newcommand{\DO}{\keyword{DO} \+ \\}
\newcommand{\RETURN}{\keyword{RETURN} }

\newcommand{\mc}{\texttt{mc}}

\newcommand{\linebreakByHand}{\ccTexHtml{\\}{}}
\newcommand{\SaveSpaceByHand}{}  %%%%% [2]{\ccTexHtml{#1}{#2}}

@! ============================================================================
@! Title
@! ============================================================================

\RCSdef{\rcsrevision}{$Revision$}
\RCSdefDate{\rcsdate}{$Date$}

@t vskip 5 mm
@t title titlefont centre "CGAL -- 2D Smallest Enclosing Circle*"
@t vskip 1 mm
@t title smalltitlefont centre "Implementation Documentation"
@t vskip 5 mm
@t title smalltitlefont centre "Bernd G�rtner and Sven Sch�nherr"
\smallskip
\centerline{\rcsrevision\ , \rcsdate}
@t vskip 1 mm

\renewcommand{\thefootnote}{\fnsymbol{footnote}}
\footnotetext[1]{This work was supported by the ESPRIT IV LTR Project
  No.~21957 (CGAL).}

@! ============================================================================
@! Introduction and Contents
@! ============================================================================

\section*{Introduction}

We provide an implementation of an optimisation algorithm for computing
the smallest (w.r.t.\ area) enclosing circle of a finite point set $P$
in the plane. The class template \ccc{CGAL_Min_circle_2} is implemented
as a semi-dynamic data structure, thus allowing to insert points while
maintaining the smallest enclosing circle. It is parameterized with a
traits class, that defines the abstract interface between the
optimisation algorithm and the primitives it uses. For ease of use, we
provide traits class adapters that interface the optimisation algorithm
with user supplied point classes.

This document is organized as follows. The algorithm is described in
Section~1. Section~2 contains the specifications as they appear in the
CGAL Reference Manual. Section~3 gives the implementations. In
Section~4 we provide a test program which performs some correctness
checks. Finally the product files are created in Section~5.

\tableofcontents

@! ============================================================================
@! The Algorithm
@! ============================================================================

\clearpage
\section{The Algorithm} \label{sec:algo}

The implementation is based on an algorithm by Welzl~\cite{w-sedbe-91a},
which we shortly describe now. The smallest (w.r.t.\ area) enclosing
circle of a finite point set $P$ in the plane, denoted by $mc(P)$, is
built up incrementally, adding one point after another. Assume $mc(P)$
has been constructed, and we would like to obtain $mc(P \cup \{p\})$, $p$
some new point. There are two cases: if $p$ already lies inside $mc(P)$,
then $mc(P \cup \{p\}) = mc(P)$. Otherwise $p$ must lie on the boundary
of $mc(P \cup \{p\})$ (this is proved in~\cite{w-sedbe-91a} and not hard
to see), so we need to compute $mc(P,\{p\})$, the smallest circle
enclosing $P$ with $p$ on the boundary. This is recursively done in the
same manner. In general, for point sets $P$,$B$, define $mc(P,B)$ as the
smallest circle enclosing $P$ that has the points of $B$ on the boundary
(if defined). Although the algorithm finally delivers a circle
$mc(P,\emptyset)$, it internally deals with circles that have a possibly
nonempty set $B$. Here is the pseudocode of Welzl's method.  To compute
$mc(P)$, it is called with the pair $(P,\emptyset)$, assuming that
$P=\{p_1,\ldots,p_n\}$ is stored in a linked list.

\begin{pseudocode}{$\mc(P,B)$:}
  $mc := \mc(\emptyset,B)$ \\
  \IF $|B| = 3$ \keyword{THEN} \RETURN $mc$ \\
  \FOR $i := 1$ \TO $n$ \DO
      \IF $p_i \not\in mc$ \THEN
          $mc := \mc(\{p_1,\ldots,p_{i-1}\}, B \cup \{p_i\})$ \\
          move $p_i$ to the front of $P$ \\
      \END
  \END
  \RETURN $mc$ \\
\end{pseudocode}

Note the following: (a) $|B|$ is always bounded by 3, thus the
computation of $mc(\emptyset,B)$ is easy. In our implementation, it is
done by the private member function \ccc{compute_circle}. (b) One can
check that the method maintains the invariant `$mc(P,B)$ exists'. This
justifies termination if $|B| = 3$, because then $mc(P,B)$ must be the
unique circle with the points of $B$ on the boundary, and $mc(P,B)$
exists if and only if this circle contains the points of $P$. Thus, no
subsequent in-circle tests are necessary anymore (for details
see~\cite{w-sedbe-91a}). (c) points which are found to lie outside the
current circle $mc$ are considered `important' and are moved to the front
of the linked list that stores $P$. This is crucial for the method's
efficiency.

It can also be advisable to bring $P$ into random order before
computation starts. There are `bad' insertion orders which cause the
method to be very slow -- random shuffling gives these orders a very
small probability.

@! ============================================================================
@! Specifications
@! ============================================================================

\clearpage
\section{Specifications}

\emph{Note:} Below some references are undefined, they refer to sections
in the \cgal\ Reference Manual.

\renewcommand{\ccSection}{\ccSubsection}
\renewcommand{\ccFont}{\tt}
\renewcommand{\ccEndFont}{}
\ccSetThreeColumns{typedef CGAL_Point_2<R>}{}{%
  creates a variable \ccc{min_circle} of type \ccc{CGAL_Min_circle_2<Traits>}.}
\ccPropagateThreeToTwoColumns
\input{../../doc_tex/basic/Optimisation/Min_circle_2.tex}
\input{../../doc_tex/basic/Optimisation/Optimisation_circle_2.tex}
\input{../../doc_tex/basic/Optimisation/Min_circle_2_adapterC2.tex}
\input{../../doc_tex/basic/Optimisation/Min_circle_2_adapterH2.tex}

@! ============================================================================
@! Implementations
@! ============================================================================

\clearpage
\section{Implementations}

@! ----------------------------------------------------------------------------
@! Class template CGAL_Min_circle_2<Traits>
@! ----------------------------------------------------------------------------

\subsection{Class template \ccFont CGAL\_Min\_circle\_2<Traits>}

First, we declare the class template \ccc{CGAL_Min_circle_2}.

@macro<Min_circle_2 declaration> = @begin
    template < class _Traits >
    class CGAL_Min_circle_2;
@end

The actual work of the algorithm is done in the private member
functions \ccc{mc} and \ccc{compute_circle}. The former directly
realizes the pseudocode of $\mc(P,B)$, the latter solves the basic
case $\mc(\emptyset,B)$, see Section~\ref{sec:algo}.

\emph{Workaround:} The GNU compiler (g++ 2.7.2[.?]) does not accept types
with scope operator as argument type or return type in class template
member functions. Therefore, all member functions are implemented in
the class interface.

The class interface looks as follows.

@macro <Min_circle_2 interface> = @begin
    template < class _Traits >
    class CGAL_Min_circle_2 {
      public:
        @<Min_circle_2 public interface>

      private:
        // private data members
        @<Min_circle_2 private data members>

        // copying and assignment not allowed!
        CGAL_Min_circle_2( CGAL_Min_circle_2<_Traits> const&);
        CGAL_Min_circle_2<_Traits>&
            operator = ( CGAL_Min_circle_2<_Traits> const&);

    @<dividing line>

    // Class implementation
    // ====================

      public:
        // Access functions and predicates
        // -------------------------------
        @<Min_circle_2 access functions `number_of_...'>

        @<Min_circle_2 predicates `is_...'>

        @<Min_circle_2 access functions>

        @<Min_circle_2 predicates>

      private:
        // Privat member functions
        // -----------------------
        @<Min_circle_2 private member function `compute_circle'>

        @<Min_circle_2 private member function `mc'>

      public:
        // Constructors
        // ------------
        @<Min_circle_2 constructors>

        // Destructor
        // ----------
        @<Min_circle_2 destructor>

        // Modifiers
        // ---------
        @<Min_circle_2 modifiers>

        // Validity check
        // --------------
        @<Min_circle_2 validity check>

        // Miscellaneous
        // -------------
        @<Min_circle_2 miscellaneous>
    };
@end   

@! ----------------------------------------------------------------------------
\subsubsection{Public Interface}

The functionality is described and documented in the specification
section, so we do not comment on it here.

@macro <Min_circle_2 public interface> = @begin
    // types
    typedef           _Traits                      Traits;
    typedef typename  _Traits::Point               Point;
    typedef typename  _Traits::Circle              Circle;
    typedef typename  list<Point>::const_iterator  Point_iterator;
    typedef           const Point *                Support_point_iterator;

    /**************************************************************************
    WORKAROUND: The GNU compiler (g++ 2.7.2[.*]) does not accept types
    with scope operator as argument type or return type in class template
    member functions. Therefore, all member functions are implemented in
    the class interface.

    // creation
    CGAL_Min_circle_2( const Point*  first,
                       const Point*  last,
                       bool          randomize = false,
                       CGAL_Random&  random    = CGAL_random,
                       Traits const& traits    = Traits());
    CGAL_Min_circle_2( list<Point>::const_iterator first,
                       list<Point>::const_iterator last,
                       bool          randomize = false,
                       CGAL_Random&  random    = CGAL_random,
                       Traits const& traits    = Traits());
    CGAL_Min_circle_2( istream_iterator<Point,ptrdiff_t> first,
                       istream_iterator<Point,ptrdiff_t> last,
                       bool          randomize = false,
                       CGAL_Random&  random    = CGAL_random,
                       Traits const& traits    = Traits())
    CGAL_Min_circle_2( Traits const& traits = Traits());
    CGAL_Min_circle_2( Point  const& p,
                       Traits const& traits = Traits());
    CGAL_Min_circle_2( Point  const& p,
                       Point  const& q,
                       Traits const& traits = Traits());
    CGAL_Min_circle_2( Point  const& p1,
                       Point  const& p2,
                       Point  const& p3,
                       Traits const& traits = Traits());
    ~CGAL_Min_circle_2( );

    // access functions
    int  number_of_points        ( ) const;
    int  number_of_support_points( ) const;

    Point_iterator  points_begin( ) const;
    Point_iterator  points_end  ( ) const;

    Support_point_iterator  support_points_begin( ) const;
    Support_point_iterator  support_points_end  ( ) const;

    Point const&  support_point( int i) const;

    Circle const&  circle( ) const;

    // predicates
    CGAL_Bounded_side  bounded_side( Point const& p) const;
    bool  has_on_bounded_side      ( Point const& p) const;
    bool  has_on_boundary          ( Point const& p) const;
    bool  has_on_unbounded_side    ( Point const& p) const;

    bool  is_empty     ( ) const;
    bool  is_degenerate( ) const;

    // modifiers
    void  insert( Point const& p);
    void  insert( const Point* first,
                  const Point* last );
    void  insert( list<Point>::const_iterator first,
                  list<Point>::const_iterator last );
    void  insert( istream_iterator<Point,ptrdiff_t> first,
                  istream_iterator<Point,ptrdiff_t> last );
    void  clear( );

    // validity check
    bool  is_valid( bool verbose = false, int level = 0) const;

    // miscellaneous
    Traits const&  traits( ) const;
    **************************************************************************/
@end

@! ----------------------------------------------------------------------------
\subsubsection{Private Data Members}

First, the traits class object is stored.

@macro <Min_circle_2 private data members> += @begin
    Traits       tco;                           // traits class object
@end

The points of $P$ are internally stored as a linked list that allows to
bring points to the front of the list in constant time. We use the
sequence container \ccc{list} from STL~\cite{sl-stl-95}.

@macro <Min_circle_2 private data members> += @begin
    list<Point>  points;                        // doubly linked list of points
@end

The support set $S$ of at most three support points is stored in an
array \ccc{support_points}, the actual number of support points is
given by \ccc{n_support_points}. During the computations, the set of
support points coincides with the set $B$ appearing in the pseudocode
for $\mc(P,B)$, see Section~\ref{sec:algo}.

\emph{Workaround:} The array of support points is allocated dynamically,
because the SGI compiler (mipspro CC 7.1) does not accept a static
array here.

@macro <Min_circle_2 private data members> += @begin
    int          n_support_points;              // number of support points
    Point*       support_points;                // array of support points
@end

Finally, the actual circle is stored in a variable \ccc{circle}
provided by the traits class object, by the end of computation equal to
$mc(P)$. During computation, \ccc{tco.circle} equals the circle $mc$
appearing in the pseudocode for $\mc(P,B)$, see Section~\ref{sec:algo}.

@! ----------------------------------------------------------------------------
\subsubsection{Constructors and Destructor}

We provide several different constructors, which can be put into two
groups. The constructors in the first group, i.e. the more important
ones, build the smallest enclosing circle $mc(P)$ from a point set $P$,
given by a begin iterator and a past-the-end iterator. Usually this
would be done by a single template member function, but since most
compilers do not support this yet, we provide specialized constructors
for C~arrays (using pointers as iterators), for STL sequence containers
\ccc{vector<Point>} and \ccc{list<Point>} and for the STL input stream
iterator \ccc{istream_iterator<Point>}.  Actually, the constructors for
a C~array and a \ccc{vector<point>} are the same, since the random
access iterator of \ccc{vector<Point>} is implemented as \ccc{Point*}.

All three constructors of the first group copy the points into the
internal list \ccc{points}. If randomization is demanded, the points
are copied to a vector and shuffled at random, before being copied to
\ccc{points}. Finally the private member function $mc$ is called to
compute $mc(P)=mc(P,\emptyset)$.

@macro <Min_circle_2 constructors> += @begin
    // STL-like constructor for C array and vector<Point>
    CGAL_Min_circle_2( const Point*  first,
                       const Point*  last,
                       bool          randomize = false,
                       CGAL_Random&  random    = CGAL_random,
                       Traits const& traits    = Traits())
        : tco( traits)
    {
        // allocate support points' array
        support_points = new Point[ 3];

        // range not empty?
        if ( ( last-first) > 0) {

            // store points
            if ( randomize) {

                // shuffle points at random
                vector<Point> v( first, last);
                random_shuffle( v.begin(), v.end(), random);
                copy( v.begin(), v.end(), back_inserter( points)); }
            else
                copy( first, last, back_inserter( points)); }

        // compute mc
        mc( points.end(), 0);
    }

    // STL-like constructor for list<Point>
    CGAL_Min_circle_2( list<Point>::const_iterator first,
                       list<Point>::const_iterator last,
                       bool          randomize = false,
                       CGAL_Random&  random    = CGAL_random,
                       Traits const& traits    = Traits())
        : tco( traits)
    {
        // allocate support points' array
        support_points = new Point[ 3];

        // compute number of points
        list<Point>::size_type n = 0;
        CGAL__distance( first, last, n);
        if ( n > 0) {

            // store points
            if ( randomize) {

                // shuffle points at random
                vector<Point> v;
                v.reserve( n);
                copy( first, last, back_inserter( v));
                random_shuffle( v.begin(), v.end(), random);
                copy( v.begin(), v.end(), back_inserter( points)); }
            else
                copy( first, last, back_inserter( points)); }

        // compute mc
        mc( points.end(), 0);
    }

    // STL-like constructor for input stream iterator istream_iterator<Point>
    CGAL_Min_circle_2( istream_iterator<Point,ptrdiff_t>  first,
                       istream_iterator<Point,ptrdiff_t>  last,
                       bool          randomize = false,
                       CGAL_Random&  random    = CGAL_random,
                       Traits const& traits    = Traits())
        : tco( traits)
    {
        // allocate support points' array
        support_points = new Point[ 3];

        // range not empty?
        if ( first != last) {

            // store points
            if ( randomize) {

                // shuffle points at random
                vector<Point> v;
                copy( first, last, back_inserter( v));
                random_shuffle( v.begin(), v.end(), random);
                copy( v.begin(), v.end(), back_inserter( points)); }
            else
                copy( first, last, back_inserter( points)); }

        // compute mc
        mc( points.end(), 0);
    }

@end

The remaining constructors are actually specializations of the
previous ones, building the smallest enclosing circle for up to three
points. The idea is the following: recall that for any point set $P$
there exists $S \subseteq P$, $|S| \leq 3$ with $mc(S) = mc(P)$ (in
fact, such a set $S$ is determined by the algorithm). Once $S$ has
been computed (or given otherwise), $mc(P)$ can easily be
reconstructed from $S$ in constant time. To make this reconstruction
more convenient, a constructor is available for each size of $|S|$,
ranging from 0 to 3. For $|S|=0$, we get the default constructor,
building $mc(\emptyset)$.

@macro <Min_circle_2 constructors> += @begin

    // default constructor
    inline
    CGAL_Min_circle_2( Traits const& traits = Traits())
        : tco( traits), n_support_points( 0)
    {
        // allocate support points' array
        support_points = new Point[ 3];

        // initialize circle
        tco.circle.set();

        CGAL_optimisation_postcondition( is_empty());
    }

    // constructor for one point
    inline
    CGAL_Min_circle_2( Point const& p, Traits const& traits = Traits())
        : tco( traits), points( 1, p), n_support_points( 1)
    {
        // allocate support points' array
        support_points = new Point[ 3];

        // initialize circle
        support_points[ 0] = p;
        tco.circle.set( p);

        CGAL_optimisation_postcondition( is_degenerate());
    }

    // constructor for two points
    inline
    CGAL_Min_circle_2( Point const& p1,
                       Point const& p2,
                       Traits const& traits = Traits())
        : tco( traits)
    {
        // allocate support points' array
        support_points = new Point[ 3];

        // store points
        points.push_back( p1);
        points.push_back( p2);

        // compute mc
        mc( points.end(), 0);
    }

    // constructor for three points
    inline
    CGAL_Min_circle_2( Point const& p1,
                       Point const& p2,
                       Point const& p3,
                       Traits const& traits = Traits())
        : tco( traits)
    {
        // allocate support points' array
        support_points = new Point[ 3];

        // store points
        points.push_back( p1);
        points.push_back( p2);
        points.push_back( p3);

        // compute mc
        mc( points.end(), 0);
    }
@end

The destructor only frees the memory of the support points' array.

@macro <Min_circle_2 destructor> = @begin
    inline
    ~CGAL_Min_circle_2( )
    {
        // free support points' array
        delete[] support_points;
    }
@end

@! ----------------------------------------------------------------------------
\subsubsection{Access Functions}

These functions are used to retrieve information about the current
status of the \ccc{CGAL_Min_circle_2<Traits>} object. They are all very
simple (and therefore \ccc{inline}) and mostly rely on corresponding
access functions of the data members of \ccc{CGAL_Min_circle_2<Traits>}.

First, we define the \ccc{number_of_...} methods.

@macro <Min_circle_2 access functions `number_of_...'> = @begin
    // #points and #support points
    inline
    int
    number_of_points( ) const
    {
        return( points.size());
    }

    inline
    int
    number_of_support_points( ) const
    {
        return( n_support_points);
    }
@end

Then, we have the access functions for points and support points.

@macro <Min_circle_2 access functions> += @begin
    // access to points and support points
    inline
    Point_iterator
    points_begin( ) const
    {
        return( points.begin());
    }

    inline
    Point_iterator
    points_end( ) const
    {
        return( points.end());
    }

    inline
    Support_point_iterator
    support_points_begin( ) const
    {
        return( support_points);
    }

    inline
    Support_point_iterator
    support_points_end( ) const
    {
        return( support_points+n_support_points);
    }

    // random access for support points    
    inline
    Point const&
    support_point( int i) const
    {
        CGAL_optimisation_precondition( (i >= 0) &&
                                        (i <  number_of_support_points()));
        return( support_points[ i]);
    }
@end

Finally, the access function \ccc{circle}.

@macro <Min_circle_2 access functions> += @begin
    // circle
    inline
    Circle const&
    circle( ) const
    {
        return( tco.circle);
    }
@end

@! ----------------------------------------------------------------------------
\subsubsection{Predicates}

The predicates \ccc{is_empty} and \ccc{is_degenerate} are used in
preconditions and postconditions of some member functions. Therefore we
define them \ccc{inline} and put them in a separate macro.

@macro <Min_circle_2 predicates `is_...'> = @begin
    // is_... predicates
    inline
    bool
    is_empty( ) const
    {
        return( number_of_support_points() == 0);
    }

    inline
    bool
    is_degenerate( ) const
    {
        return( number_of_support_points() <  2);
    }
@end

The remaining predicates perform in-circle tests, based on the
corresponding predicates of class \ccc{Circle}.

@macro <Min_circle_2 predicates> = @begin
    // in-circle test predicates
    inline
    CGAL_Bounded_side
    bounded_side( Point const& p) const
    {
        return( tco.circle.bounded_side( p));
    }

    inline
    bool
    has_on_bounded_side( Point const& p) const
    {
        return( tco.circle.has_on_bounded_side( p));
    }

    inline
    bool
    has_on_boundary( Point const& p) const
    {
        return( tco.circle.has_on_boundary( p));
    }

    inline
    bool
    has_on_unbounded_side( Point const& p) const
    {
        return( tco.circle.has_on_unbounded_side( p));
    }
@end

@! ----------------------------------------------------------------------------
\subsubsection{Modifiers}

There is another way to build up $mc(P)$, other than by supplying
the point set $P$ at once. Namely, $mc(P)$ can be built up
incrementally, adding one point after another. If you look at the
pseudocode in the introduction, this comes quite naturally. The
modifying method \ccc{insert}, applied with point $p$ to a
\ccc{CGAL_Min_circle_2<Traits>} object representing $mc(P)$,
computes $mc(P \cup \{p\})$, where work has to be done only if $p$
lies outside $mc(P)$. In this case, $mc(P \cup \{p\}) = mc(P,\{p\})$
holds, so the private member function \ccc{mc} is called with
support set $\{p\}$. After the insertion has been performed, $p$ is
moved to the front of the point list, just like in the pseudocode in
Section~\ref{sec:algo}.

@macro <Min_circle_2 modifiers> += @begin
    void
    insert( Point const& p)
    {
        // p not in current circle?
        if ( has_on_unbounded_side( p)) {

            // p new support point
            support_points[ 0] = p;

            // recompute mc
            mc( points.end(), 1);

            // store p as the first point in list
            points.push_front( p); }
        else

            // append p to the end of the list
            points.push_back( p);
    }
@end

Inserting a range of points would usually be done by a single template
member function, but since most compilers do not support this yet, we
provide specialized \ccc{insert} functions for C~arrays (using pointers
as iterators), for STL sequence containers \ccc{vector<Point>} and
\ccc{list<Point>} and for the STL input stream iterator
\ccc{istream_iterator<Point>}. Actually, the \ccc{insert} function for
a C~array and a \ccc{vector<point>} are the same, since the random
access iterator of \ccc{vector<Point>} is implemented as \ccc{Point*}.

The following \ccc{insert} functions perform for each point \ccc{p} in
the range $[\mbox{\ccc{first}},\mbox{\ccc{last}})$ a call \ccc{insert(p)}.

@macro <Min_circle_2 modifiers> += @begin
    inline
    void
    insert( const Point* first, const Point* last)
    {
        for ( ; first != last; ++first)
            insert( *first);
    }

    inline
    void
    insert( list<Point>::const_iterator first,
            list<Point>::const_iterator last )
    {
        for ( ; first != last; ++first)
            insert( *first);
    }

    inline
    void
    insert( istream_iterator<Point,ptrdiff_t>  first,
            istream_iterator<Point,ptrdiff_t>  last )
    {
        for ( ; first != last; ++first)
            insert( *first);
    }
@end

The member function \ccc{clear} deletes all points from a
\ccc{CGAL_Min_circle_2<Traits>} object and resets it to the
empty circle.

@macro <Min_circle_2 modifiers> += @begin
    void  clear( )
    {
        points.erase( points.begin(), points.end());
        n_support_points = 0;
        tco.circle.set();
    }
@end

@! ----------------------------------------------------------------------------
\subsubsection{Validity Check}

A \ccc{CGAL_Min_circle_2<Traits>} object can be checked for validity.
This means, it is checked whether (a) the circle contains all points
of its defining set $P$, (b) the circle is the smallest circle spanned
by its support set, and (c) the support set is minimal, i.e.\ no
support point is redundant. The function \ccc{is_valid} is mainly
intended for debugging user supplied traits classes but also for
convincing the anxious user that the traits class implementation is
correct. If \ccc{verbose} is \ccc{true}, some messages concerning the
performed checks are written to standard error stream. The second
parameter \ccc{level} is not used, we provide it only for consistency
with interfaces of other classes.

@macro <Min_circle_2 validity check> = @begin
    bool
    is_valid( bool verbose = false, int level = 0) const
    {
        CGAL_Verbose_ostream verr( verbose);
        verr << endl;
        verr << "CGAL_Min_circle_2<Traits>::" << endl;
        verr << "is_valid( true, " << level << "):" << endl;
        verr << "  |P| = " << number_of_points()
             << ", |S| = " << number_of_support_points() << endl;

        // containment check (a)
        @<Min_circle_2 containment check>

        // support set checks (b)+(c)
        @<Min_circle_2 support set checks>

        verr << "  object is valid!" << endl;
        return( true);
    }
@end

The containment check (a) is easy to perform, just a loop over all
points in \ccc{points}.

@macro <Min_circle_2 containment check> = @begin
    verr << "  a) containment check..." << flush;
    Point_iterator point_iter;
    for ( point_iter  = points_begin();
          point_iter != points_end();
          ++point_iter)
        if ( has_on_unbounded_side( *point_iter)) 
            return( CGAL__optimisation_is_valid_fail( verr,
                        "circle does not contain all points"));
    verr << "passed." << endl;
@end

To check the support set (b) and (c), we distinguish four cases with
respect to the number of support points, which may range from 0 to 3.

@macro <Min_circle_2 support set checks> = @begin
    verr << "  b)+c) support set checks..." << flush;
    switch( number_of_support_points()) {

      case 0:
        @<Min_circle_2 check no support point>
        break;

      case 1:
        @<Min_circle_2 check one support point>
        break;

      case 2: {
        @<Min_circle_2 check two support points> }
        break;

      case 3: {
        @<Min_circle_2 check three support points> }
        break;

      default:
        return( CGAL__optimisation_is_valid_fail( verr,
                    "illegal number of support points, \
                     not between 0 and 3."));
    };
    verr << "passed." << endl;
@end

The case of no support point happens if and only if the defining
point set $P$ is empty.

@macro <Min_circle_2 check no support point> = @begin
    if ( ! is_empty())
        return( CGAL__optimisation_is_valid_fail( verr,
                    "P is nonempty, \
                     but there are no support points."));
@end

If the smallest enclosing circle has one support point $p$, it must
be equal to that point, i.e.\ its center must be $p$ and its radius
$0$.

@macro <Min_circle_2 check one support point> = @begin
    if ( ( circle().center() != support_point( 0)    ) ||
         ( ! CGAL_is_zero( circle().squared_radius())) )
        return( CGAL__optimisation_is_valid_fail( verr,
                    "circle differs from the circle \
                     spanned by its single support point."));
@end

In case of two support points $p,q$, these points must form a diameter
of the circle. The support set $\{p,q\}$ is minimal if and only if
$p,q$ are distinct.

The diameter property is checked as follows. If $p$ and $q$ both lie
on the circle's boundary and if $p$, $q$ (knowing they are distinct)
and the circle's center are collinear, then $p$ and $q$ form a
diameter of the circle.

@macro <Min_circle_2 check two support points> = @begin
    Point const& p( support_point( 0)),
                 q( support_point( 1));

    // p equals q?
    if ( p == q)
        return( CGAL__optimisation_is_valid_fail( verr,
                    "the two support points are equal."));

    // segment(p,q) is not diameter?
    if ( ( ! has_on_boundary( p)                                ) ||
         ( ! has_on_boundary( q)                                ) ||
         ( tco.orientation( p, q,
			    circle().center()) != CGAL_COLLINEAR) )
        return( CGAL__optimisation_is_valid_fail( verr,
                    "circle does not have its \
                     two support points as diameter."));
@end

If the number of support points is three (and they are distinct and
not collinear), the circle through them is unique, and must therefore
equal the current circle stored in \ccc{circle}. It is the smallest
one containing the three points if and only if the center of the
circle lies inside or on the boundary of the triangle defined by the
three points. The support set is minimal only if the center lies
properly inside the triangle.

Both triangle properties are checked by comparing the orientations of
three point triples, each containing two of the support points and the
center of the current circle, resp. If one of these orientations equals
\ccc{CGAL_COLLINEAR}, the center lies on the boundary of the triangle.
Otherwise, if two triples have opposite orientations, the center is not
contained in the triangle.

@macro <Min_circle_2 check three support points> = @begin
    Point const& p( support_point( 0)),
                 q( support_point( 1)),
                 r( support_point( 2));

    // p, q, r not pairwise distinct?
    if ( ( p == q) || ( q == r) || ( r == p))
        return( CGAL__optimisation_is_valid_fail( verr,
                    "at least two of the three \
                     support points are equal."));

    // p, q, r collinear?
    if ( tco.orientation( p, q, r) == CGAL_COLLINEAR)
        return( CGAL__optimisation_is_valid_fail( verr,
                    "the three support points are collinear."));

    // current circle not equal the unique circle through p,q,r ?
    Circle c( circle());
    c.set( p, q, r);
    if ( circle() != c)
        return( CGAL__optimisation_is_valid_fail( verr,
                    "circle is not the unique circle \
                     through its three support points."));

    // circle's center on boundary of triangle(p,q,r)?
    Point const& center( circle().center());
    CGAL_Orientation o_pqz = tco.orientation( p, q, center);
    CGAL_Orientation o_qrz = tco.orientation( q, r, center);
    CGAL_Orientation o_rpz = tco.orientation( r, p, center);
    if ( ( o_pqz == CGAL_COLLINEAR) ||
         ( o_qrz == CGAL_COLLINEAR) ||
         ( o_rpz == CGAL_COLLINEAR) )
        return( CGAL__optimisation_is_valid_fail( verr,
                    "one of the three support points is redundant."));

    // circle's center not inside triangle(p,q,r)?
    if ( ( o_pqz != o_qrz) || ( o_qrz != o_rpz) || ( o_rpz != o_pqz))
        return( CGAL__optimisation_is_valid_fail( verr,
                    "circle's center is not in the \
                     convex hull of its three support points."));
@end

@! ----------------------------------------------------------------------------
\subsubsection{Miscellaneous}

The member function \ccc{traits} returns a const reference to the
traits class object.

@macro <Min_circle_2 miscellaneous> = @begin
    inline
    Traits const&
    traits( ) const
    {
        return( tco);
    }
@end

@! ----------------------------------------------------------------------------
\subsubsection{I/O}

@macro <Min_circle_2 I/O operators declaration> = @begin
    template < class _Traits >
    ostream& operator << ( ostream& os, CGAL_Min_circle_2<_Traits> const& mc);

    template < class _Traits >
    istream& operator >> ( istream& is, CGAL_Min_circle_2<_Traits>      & mc);
@end

@macro <Min_circle_2 I/O operators> = @begin
    template < class _Traits >
    ostream&
    operator << ( ostream& os, CGAL_Min_circle_2<_Traits> const& min_circle)
    {
        typedef typename  CGAL_Min_circle_2<_Traits>::Point  Point;

        switch ( CGAL_get_mode( os)) {

          case CGAL_IO::PRETTY:
            os << endl;
            os << "CGAL_Min_circle_2( |P| = " << min_circle.number_of_points()
               << ", |S| = " << min_circle.number_of_support_points() << endl;
            os << "  P = {" << endl;
            os << "    ";
            copy( min_circle.points_begin(), min_circle.points_end(),
                  ostream_iterator<Point>( os, ",\n    "));
            os << "}" << endl;
            os << "  S = {" << endl;
            os << "    ";
            copy( min_circle.support_points_begin(),
                  min_circle.support_points_end(),
                  ostream_iterator<Point>( os, ",\n    "));
            os << "}" << endl;
            os << "  circle = " << min_circle.circle() << endl;
            os << ")" << endl;
            break;

          case CGAL_IO::ASCII:
            copy( min_circle.points_begin(), min_circle.points_end(),
                  ostream_iterator<Point>( os, "\n"));
            break;

          case CGAL_IO::BINARY:
            copy( min_circle.points_begin(), min_circle.points_end(),
                  ostream_iterator<Point>( os));
            break;

          default:
            CGAL_optimisation_assertion_msg( false,
                                             "CGAL_get_mode( os) invalid!");
            break; }

        return( os);
    }

    template < class Traits >
    istream&
    operator >> ( istream& is, CGAL_Min_circle_2<Traits>& min_circle)
    {
        switch ( CGAL_get_mode( is)) {

          case CGAL_IO::PRETTY:
            cerr << endl;
            cerr << "Stream must be in ascii or binary mode" << endl;
            break;

          case CGAL_IO::ASCII:
          case CGAL_IO::BINARY:
            typedef typename  CGAL_Min_circle_2<Traits>::Point   Point;
            typedef           istream_iterator<Point,ptrdiff_t>  Is_it;
            min_circle.clear();
            min_circle.insert( Is_it( is), Is_it());
            break;

          default:
            CGAL_optimisation_assertion_msg( false, "CGAL_IO::mode invalid!");
            break; }

        return( is);
    }
@end

@! ----------------------------------------------------------------------------
\subsubsection{Private Member Function {\ccFont compute\_circle}}

This is the method for computing the basic case $\mc(\emptyset,B)$,
the set $B$ given by the first \ccc{n_support_points} in the array
\ccc{support_points}. It is realized by a simple case analysis,
noting that $|B| \leq 3$.

@macro <Min_circle_2 private member function `compute_circle'> = @begin
    // compute_circle
    inline
    void
    compute_circle( )
    {
        switch ( n_support_points) {
          case 3:
            tco.circle.set( support_points[ 0],
                            support_points[ 1],
                            support_points[ 2]);
            break;
          case 2:
            tco.circle.set( support_points[ 0], support_points[ 1]);
            break;
          case 1:
            tco.circle.set( support_points[ 0]);
            break;
          case 0:
            tco.circle.set( );
            break;
          default:
            CGAL_optimisation_assertion( ( n_support_points >= 0) &&
                                         ( n_support_points <= 3) ); }
    }
@end

@! ----------------------------------------------------------------------------
\subsubsection{Private Member Function {\ccFont mc}}

This function computes the general circle $mc(P,B)$, where $P$ contains
the points in the range $[$\ccc{points.begin()}$,$\ccc{last}$)$ and $B$
is given by the first \ccc{n_sp} support points in the array
\ccc{support_points}. The function is directly modelled after the
pseudocode above.

@macro <Min_circle_2 private member function `mc'> = @begin
    void
    mc( Point_iterator const& last, int n_sp)
    {
        // compute circle through support points
        n_support_points = n_sp;
        compute_circle();
        if ( n_sp == 3) return;

        // test first n points
        list<Point>::iterator  point_iter( points.begin());
        for ( ; last != point_iter; ) {
            Point const& p( *point_iter);

            // p not in current circle?
            if ( has_on_unbounded_side( p)) {

                // recursive call with p as additional support point
                support_points[ n_sp] = p;
                mc( point_iter, n_sp+1);

                // move current point to front
                if ( point_iter != points.begin()) {            // p not first?
                    points.push_front( p);
                    points.erase( point_iter++); }
                else
                    ++point_iter; }
            else
                ++point_iter; }
    }
@end

@! ----------------------------------------------------------------------------
@! Class template CGAL_Optimisation_circle_2<R>
@! ----------------------------------------------------------------------------

\subsection{Class template \ccFont CGAL\_Optimisation\_circle\_2<R>}

First, we declare the class template \ccc{CGAL_Optimisation_circle_2},

@macro<Optimisation_circle_2 declaration> = @begin
    template < class _R >
    class CGAL_Optimisation_circle_2;
@end

\emph{Workaround:} The GNU compiler (g++ 2.7.2[.?]) does not accept types
with scope operator as argument type or return type in class template
member functions. Therefore, all member functions are implemented in
the class interface.

The class interface looks as follows.

@macro <Optimisation_circle_2 interface> = @begin
    template < class _R >
    class CGAL_Optimisation_circle_2 {
      public:
        @<Optimisation_circle_2 public interface>

      private:
        // private data members
        @<Optimisation_circle_2 private data members>

    @<dividing line>

    // Class implementation
    // ====================

      public:
        // Set functions
        // -------------
        @<Optimisation_circle_2 set functions>

        // Access functions
        // ----------------
        @<Optimisation_circle_2 access functions>

        // Equality tests
        // --------------
        @<Optimisation_circle_2 equality tests>

        // Predicates
        // ----------
        @<Optimisation_circle_2 predicates>
    };
@end   

@! ----------------------------------------------------------------------------
\subsubsection{Public Interface}

The functionality is described and documented in the specification
section, so we do not comment on it here.

@macro <Optimisation_circle_2 public interface> = @begin
    // types
    typedef           _R               R;
    typedef           CGAL_Point_2<R>  Point;
    typedef typename  _R::FT           Distance;

    /**************************************************************************
    WORKAROUND: The GNU compiler (g++ 2.7.2[.*]) does not accept types
    with scope operator as argument type or return type in class template
    member functions. Therefore, all member functions are implemented in
    the class interface.

    // creation
    void  set( );
    void  set( Point const& p);
    void  set( Point const& p, Point const& q);
    void  set( Point const& p, Point const& q, Point const& r);
    void  set( Point const& center, Distance const& squared_radius);

    // access functions    
    Point    const&  center        ( ) const;
    Distance const&  squared_radius( ) const

    // equality tests
    bool  operator == ( CGAL_Optimisation_circle_2<R> const& c) const;
    bool  operator != ( CGAL_Optimisation_circle_2<R> const& c) const;

    // predicates
    CGAL_Bounded_side  bounded_side( Point const& p) const;
    bool  has_on_bounded_side      ( Point const& p) const;
    bool  has_on_boundary          ( Point const& p) const;
    bool  has_on_unbounded_side    ( Point const& p) const;

    bool  is_empty     ( ) const;
    bool  is_degenerate( ) const;
    **************************************************************************/
@end

@! ----------------------------------------------------------------------------
\subsubsection{Private Data Members}

The circle is represented by its center and squared radius.

@macro <Optimisation_circle_2 private data members> = @begin
    Point     _center;
    Distance  _squared_radius;
@end

@! ----------------------------------------------------------------------------
\subsubsection{Set Functions}

We provide set functions taking zero, one, two, or three boundary
points and another set function taking a center point and a squared
radius.

@macro <Optimisation_circle_2 set functions> = @begin
    inline
    void
    set( )
    {
        _center         =  Point( CGAL_ORIGIN);
        _squared_radius = -Distance( 1);
    }
    
    inline
    void
    set( Point const& p)
    {
        _center         = p;
        _squared_radius = Distance( 0);
    }
    
    inline
    void
    set( Point const& p, Point const& q)
    {
        _center         = CGAL_midpoint( p, q);
        _squared_radius = CGAL_squared_distance( p, _center);
    }
    
    inline
    void
    set( Point const& p, Point const& q, Point const& r)
    {
        _center         = CGAL_circumcenter( p, q, r);
        _squared_radius = CGAL_squared_distance( p, _center);
    }
    
    inline
    void
    set( Point const& center, Distance const& squared_radius)
    {
        _center         = center;
        _squared_radius = squared_radius;
    }
@end

@! ----------------------------------------------------------------------------
\subsubsection{Access Functions}

These functions are used to get the current center point or
squared radius, resp.

@macro <Optimisation_circle_2 access functions> = @begin
    inline
    Point const&
    center( ) const
    {
        return( _center);
    }

    inline
    Distance const&
    squared_radius( ) const
    {
        return( _squared_radius);
    }
@end

@! ----------------------------------------------------------------------------
\subsubsection{Equality Tests}

@macro <Optimisation_circle_2 equality tests> = @begin
    bool
    operator == ( CGAL_Optimisation_circle_2<R> const& c) const
    {
        return( ( _center          == c._center        ) &&
                ( _squared_radius  == c._squared_radius) );
    }
    
    bool
    operator != ( CGAL_Optimisation_circle_2<R> const& c) const
    {
        return( ! operator==( c));
    }
@end
    
@! ----------------------------------------------------------------------------
\subsubsection{Predicates}

The following predicates perform in-circle tests and check for
emptyness and degeneracy, resp.

@macro <Optimisation_circle_2 predicates> = @begin
    inline
    CGAL_Bounded_side
    bounded_side( Point const& p) const
    {
        return( CGAL_static_cast( CGAL_Bounded_side,
                                  CGAL_sign( CGAL_squared_distance( p, _center)
                                             - _squared_radius)));
    }

    inline
    bool
    has_on_bounded_side( Point const& p) const
    {
        return( CGAL_squared_distance( p, _center) < _squared_radius);
    }

    inline
    bool
    has_on_boundary( Point const& p) const
    {
        return( CGAL_squared_distance( p, _center) == _squared_radius);
    }

    inline
    bool
    has_on_unbounded_side( Point const& p) const
    {
        return( _squared_radius < CGAL_squared_distance( p, _center));
    }

    inline
    bool
    is_empty( ) const
    {
        return( CGAL_is_negative( _squared_radius));
    }

    inline
    bool
    is_degenerate( ) const
    {
        return( ! CGAL_is_positive( _squared_radius));
    }
@end

@! ----------------------------------------------------------------------------
\subsubsection{I/O}

@macro <Optimisation_circle_2 I/O operators declaration> = @begin
    template < class _R >
    ostream&
    operator << ( ostream& os, CGAL_Optimisation_circle_2<_R> const& c);

    template < class _R >
    istream&
    operator >> ( istream& is, CGAL_Optimisation_circle_2<_R>      & c);
@end

@macro <Optimisation_circle_2 I/O operators> = @begin
    template < class _R >
    ostream&
    operator << ( ostream& os, CGAL_Optimisation_circle_2<_R> const& c)
    {
        switch ( CGAL_get_mode( os)) {

          case CGAL_IO::PRETTY:
            os << "CGAL_Optimisation_circle_2( "
               << c.center() << ", "
               << c.squared_radius() << ')';
            break;

          case CGAL_IO::ASCII:
            os << c.center() << ' ' << c.squared_radius();
            break;

          case CGAL_IO::BINARY:
            os << c.center();
            CGAL_write( os, c.squared_radius());
            break;

          default:
            CGAL_optimisation_assertion_msg( false,
                                             "CGAL_get_mode( os) invalid!");
            break; }

        return( os);
    }

    template < class _R >
    istream&
    operator >> ( istream& is, CGAL_Optimisation_circle_2<_R>& c)
    {
        typedef typename  CGAL_Optimisation_circle_2<_R>::Point     Point;
        typedef typename  CGAL_Optimisation_circle_2<_R>::Distance  Distance;

        switch ( CGAL_get_mode( is)) {

          case CGAL_IO::PRETTY:
            cerr << endl;
            cerr << "Stream must be in ascii or binary mode" << endl;
            break;

          case CGAL_IO::ASCII: {
            Point     center;
            Distance  squared_radius;
            is >> center >> squared_radius;
            c.set( center, squared_radius); }
            break;

          case CGAL_IO::BINARY: {
            Point     center;
            Distance  squared_radius;
            is >> center;
            CGAL_read( is, squared_radius);
            c.set( center, squared_radius); }
            break;

          default:
            CGAL_optimisation_assertion_msg( false,
                                             "CGAL_get_mode( is) invalid!");
            break; }

        return( is);
    }
@end

@! ----------------------------------------------------------------------------
@! Class template CGAL_Min_circle_2_adapterC2<PT,DA>
@! ----------------------------------------------------------------------------

\subsection{Class template \ccFont CGAL\_Min\_circle\_2\_adapterC2<PT,DA>}

First, we declare the class templates \ccc{CGAL_Min_circle_2},
\ccc{CGAL_Min_circle_2_adapterC2} and
\ccc{CGAL__Min_circle_2_adapterC2__Circle}.

@macro<Min_circle_2_adapterC2 declarations> = @begin
    template < class _Traits >
    class CGAL_Min_circle_2;

    template < class _PT, class _DA >
    class CGAL_Min_circle_2_adapterC2;

    template < class _PT, class _DA >
    class CGAL__Min_circle_2_adapterC2__Circle;
@end

The actual work of the adapter is done in the nested class
\ccc{Circle}. Therefore, we implement the whole adapter in its
interface.

The variable \ccc{circle} containing the current circle is declared
\ccc{private} to disallow the user from directly accessing or modifying
it. Since the algorithm needs to access and modify the current circle,
it is declared \ccc{friend}.

@macro <Min_circle_2_adapterC2 interface and implementation> = @begin
    template < class _PT, class _DA >
    class CGAL_Min_circle_2_adapterC2 {
      public:
        // types
        typedef  _PT  PT;
        typedef  _DA  DA;

        // nested types
        typedef  PT                                           Point;
        typedef  CGAL__Min_circle_2_adapterC2__Circle<PT,DA>  Circle;

      private:
        DA      dao;                                    // data accessor object
        Circle  circle;                                 // current circle
        friend  class CGAL_Min_circle_2< CGAL_Min_circle_2_adapterC2<PT,DA> >;

      public:
        // creation
        @<Min_circle_2_adapterC2 constructors>

        // operations
        @<Min_circle_2_adapterC2 operations>
    };
@end   

@! ----------------------------------------------------------------------------
\subsubsection{Constructors}

@macro <Min_circle_2_adapterC2 constructors> = @begin
    CGAL_Min_circle_2_adapterC2( DA const& da = DA())
        : dao( da), circle( da)
    { }
@end

@! ----------------------------------------------------------------------------
\subsubsection{Operations}

@macro <Min_circle_2_adapterC2 operations> = @begin
    CGAL_Orientation
    orientation( Point const& p, Point const& q, Point const& r) const
    {
        typedef  _DA::FT  FT;

        FT  px;
        FT  py;
        FT  qx;
        FT  qy;
        FT  rx;
        FT  ry;
        
        dao.get( p, px, py);
        dao.get( q, qx, qy);
        dao.get( r, rx, ry);

        return( CGAL_static_cast( CGAL_Orientation,
                    CGAL_sign( ( px-rx) * ( qy-ry) - ( py-ry) * ( qx-rx))));
    }
@end

@! ----------------------------------------------------------------------------
\subsubsection{Nested Type \ccFont Circle}

@macro <Min_circle_2_adapterC2 nested type `Circle'> = @begin
    template < class _PT, class _DA >
    class CGAL__Min_circle_2_adapterC2__Circle {
      public:
        // typedefs
        typedef  _PT  PT;
        typedef  _DA  DA;

        typedef  _DA::FT  FT;

      private:
        // data members
        DA  dao;                                // data accessor object

        FT  center_x;                           // center's x-coordinate
        FT  center_y;                           // center's y-coordinate
        FT  sqr_rad;                            // squared radius

        // private member functions
        FT
        sqr_dist( FT const& px, FT const& py, FT const& qx, FT const& qy) const
        {
            FT  dx( px - qx);
            FT  dy( py - qy);
            return( dx*dx + dy*dy);
        }

      public:
        // types
        typedef  PT  Point;
        typedef  FT  Distance;

        // creation
        CGAL__Min_circle_2_adapterC2__Circle( DA const& da) : dao( da) { }

        void  set( )
        {
            center_x =  FT( 0);
            center_y =  FT( 0);
            sqr_rad  = -FT( 1);
        }

        void  set( Point const& p)
        {
            dao.get( p, center_x, center_y);
            sqr_rad = FT( 0);
        }

        void  set( Point const& p, Point const& q)
        {
            FT  px;
            FT  py;
            FT  qx;
            FT  qy;

            dao.get( p, px, py);
            dao.get( q, qx, qy);

            center_x = ( px+qx) / FT( 2);
            center_y = ( py+qy) / FT( 2);
            sqr_rad  = sqr_dist( px, py, center_x, center_y);
        }

        void  set( Point const& p, Point const& q, Point const& r)
        {
            FT  px;
            FT  py;
            FT  qx;
            FT  qy;
            FT  rx;
            FT  ry;

            dao.get( p, px, py);
            dao.get( q, qx, qy);
            dao.get( r, rx, ry);

            FT  qx_px( qx - px);
            FT  qy_py( qy - py);
            FT  rx_px( rx - px);
            FT  ry_py( ry - py);
 
            FT  p2   ( px*px + py*py);
            FT  q2_p2( qx*qx + qy*qy - p2); 
            FT  r2_p2( rx*rx + ry*ry - p2); 
            FT  denom( ( qx_px*ry_py - rx_px*qy_py) * FT( 2));

            center_x = ( q2_p2*ry_py - r2_p2*qy_py) / denom;
            center_y = ( r2_p2*qx_px - q2_p2*rx_px) / denom;
            sqr_rad  = sqr_dist( px, py, center_x, center_y);
        }

        // predicates
        CGAL_Bounded_side
        bounded_side( Point const& p) const
        {
            FT  px;
            FT  py;
            dao.get( p, px, py);
            return( CGAL_static_cast( CGAL_Bounded_side,
                CGAL_sign( sqr_dist( px, py, center_x, center_y) - sqr_rad)));
        }

        bool
        has_on_bounded_side( Point const& p) const
        {
            FT  px;
            FT  py;
            dao.get( p, px, py);
            return( sqr_dist( px, py, center_x, center_y) < sqr_rad);
        }

        bool
        has_on_boundary( Point const& p) const
        {
            FT  px;
            FT  py;
            dao.get( p, px, py);
            return( sqr_dist( px, py, center_x, center_y) == sqr_rad);
        }

        bool
        has_on_unbounded_side( Point const& p) const
        {
            FT  px;
            FT  py;
            dao.get( p, px, py);
            return( sqr_rad < sqr_dist( px, py, center_x, center_y));
        }

        bool
        is_empty( ) const
        {
            return( CGAL_is_negative( sqr_rad));
        }

        bool
        is_degenerate( ) const
        {
            return( ! CGAL_is_positive( sqr_rad));
        }

        // additional operations for checking
        bool
        operator == (
            CGAL__Min_circle_2_adapterC2__Circle<_PT,_DA> const& c) const
        {
            return( ( center_x == c.center_x) &&
                    ( center_y == c.center_y) &&
                    ( sqr_rad  == c.sqr_rad ) );
        }

        Point
        center( ) const
        {
            Point  p;
            dao.set( p, center_x, center_y);
            return( p);
        }

        Distance const&
        squared_radius( ) const
        {
            return( sqr_rad);
        }

        // I/O
        friend
        ostream&
        operator << ( ostream& os,
                      CGAL__Min_circle_2_adapterC2__Circle<_PT,_DA> const& c)
        {
            switch ( CGAL_get_mode( os)) {

              case CGAL_IO::PRETTY:
                os << "CGAL_Min_circle_2_adapterC2::Circle( "
                   << c.center_x << ", "
                   << c.center_y << ", "
                   << c.sqr_rad  << ')';
                break;

              case CGAL_IO::ASCII:
                os << c.center_x << ' ' << c.center_y << ' ' << c.sqr_rad;
                break;

              case CGAL_IO::BINARY:
                CGAL_write( os, c.center_x);
                CGAL_write( os, c.center_y);
                CGAL_write( os, c.sqr_rad);
                break;

              default:
                CGAL_optimisation_assertion_msg( false,
                                                "CGAL_get_mode( os) invalid!");
                break; }

            return( os);
        }

        friend
        istream&
        operator >> ( istream& is,
                      CGAL__Min_circle_2_adapterC2__Circle<_PT,_DA>& c)
        {
            switch ( CGAL_get_mode( is)) {

              case CGAL_IO::PRETTY:
                cerr << endl;
                cerr << "Stream must be in ascii or binary mode" << endl;
                break;

              case CGAL_IO::ASCII:
                is >> c.center_x >> c.center_y >> c.sqr_rad;
                break;

              case CGAL_IO::BINARY:
                CGAL_read( is, c.center_x);
                CGAL_read( is, c.center_y);
                CGAL_read( is, c.sqr_rad);
                break;

              default:
                CGAL_optimisation_assertion_msg( false,
                                                 "CGAL_IO::mode invalid!");
                break; }

            return( is);
        }
    };
@end

@! ----------------------------------------------------------------------------
@! Class template CGAL_Min_circle_2_adapterH2<PT,DA>
@! ----------------------------------------------------------------------------

\subsection{Class template \ccFont CGAL\_Min\_circle\_2\_adapterH2<PT,DA>}

First, we declare the class templates \ccc{Min_circle_2},
\ccc{CGAL_Min_circle_2_adapterH2} and
\ccc{CGAL__Min_circle_2_adapterH2__Circle}.

@macro<Min_circle_2_adapterH2 declarations> = @begin
    template < class _Traits >
    class CGAL_Min_circle_2;

    template < class _PT, class _DA >
    class CGAL_Min_circle_2_adapterH2;

    template < class _PT, class _DA >
    class CGAL__Min_circle_2_adapterH2__Circle;
@end

The actual work of the adapter is done in the nested class
\ccc{Circle}. Therefore, we implement the whole adapter in its
interface.

The variable \ccc{circle} containing the current circle is declared
\ccc{private} to disallow the user from directly accessing or modifying
it. Since the algorithm needs to access and modify the current circle,
it is declared \ccc{friend}.

@macro <Min_circle_2_adapterH2 interface and implementation> = @begin
    template < class _PT, class _DA >
    class CGAL_Min_circle_2_adapterH2 {
      public:
        // types
        typedef  _PT  PT;
        typedef  _DA  DA;

        // nested types
        typedef  PT                                           Point;
        typedef  CGAL__Min_circle_2_adapterH2__Circle<PT,DA>  Circle;

      private:
        DA      dao;                                    // data accessor object
        Circle  circle;                                 // current circle
        friend  class CGAL_Min_circle_2< CGAL_Min_circle_2_adapterH2<PT,DA> >;

      public:
        // creation
        @<Min_circle_2_adapterH2 constructors>

        // operations
        @<Min_circle_2_adapterH2 operations>
    };
@end   

@! ----------------------------------------------------------------------------
\subsubsection{Constructors}

@macro <Min_circle_2_adapterH2 constructors> = @begin
    CGAL_Min_circle_2_adapterH2( DA const& da = DA())
        : dao( da), circle( da)
    { }
@end

@! ----------------------------------------------------------------------------
\subsubsection{Operations}

@macro <Min_circle_2_adapterH2 operations> = @begin
    CGAL_Orientation
    orientation( Point const& p, Point const& q, Point const& r) const
    {
        typedef  _DA::RT  RT;

        RT  phx;
        RT  phy;
        RT  phw;
        RT  qhx;
        RT  qhy;
        RT  qhw;
        RT  rhx;
        RT  rhy;
        RT  rhw;
        
        dao.get( p, phx, phy, phw);
        dao.get( q, qhx, qhy, qhw);
        dao.get( r, rhx, rhy, rhw);

        return( CGAL_static_cast( CGAL_Orientation,
                    CGAL_sign( ( phx*rhw - rhx*phw) * ( qhy*rhw - rhy*qhw)
                             - ( phy*rhw - rhy*phw) * ( qhx*rhw - rhx*qhw))));
    }
@end

@! ----------------------------------------------------------------------------
\subsubsection{Nested Type \ccFont Circle}

@macro <Min_circle_2_adapterH2 nested type `Circle'> = @begin
    template < class _PT, class _DA >
    class CGAL__Min_circle_2_adapterH2__Circle {
      public:
        // typedefs
        typedef  _PT  PT;
        typedef  _DA  DA;

        typedef  _DA::RT            RT;
        typedef  CGAL_Quotient<RT>  FT;

      private:
        // data members
        DA  dao;                                // data accessor object

        RT  center_hx;                          // center's hx-coordinate
        RT  center_hy;                          // center's hy-coordinate
        RT  center_hw;                          // center's hw-coordinate
        FT  sqr_rad;                            // squared radius

        // private member functions
        FT
        sqr_dist( RT const& phx, RT const& phy, RT const& phw,
                  RT const& qhx, RT const& qhy, RT const& qhw) const
        {
            RT  dhx( phx*qhw - qhx*phw);
            RT  dhy( phy*qhw - qhy*phw);
            RT  dhw( phw*qhw);
            return( FT( dhx*dhx + dhy*dhy, dhw*dhw));
        }

      public:
        // types
        typedef  PT  Point;
        typedef  FT  Distance;

        // creation
        CGAL__Min_circle_2_adapterH2__Circle( DA const& da) : dao( da) { }

        void  set( )
        {
            center_hx =  RT( 0);
            center_hy =  RT( 0);
            center_hw =  RT( 1);
            sqr_rad   = -FT( 1);
        }

        void  set( Point const& p)
        {
            dao.get( p, center_hx, center_hy, center_hw);
            sqr_rad = FT( 0);
        }

        void  set( Point const& p, Point const& q)
        {
            RT  phx;
            RT  phy;
            RT  phw;
            RT  qhx;
            RT  qhy;
            RT  qhw;

            dao.get( p, phx, phy, phw);
            dao.get( q, qhx, qhy, qhw);
            center_hx = ( phx*qhw + qhx*phw);
            center_hy = ( phy*qhw + qhy*phw);
            center_hw = ( phw*qhw * RT( 2));
            sqr_rad   = sqr_dist( phx, phy, phw,
                                  center_hx, center_hy, center_hw);
        }

        void  set( Point const& p, Point const& q, Point const& r)
        {
            RT  phx;
            RT  phy;
            RT  phw;
            RT  qhx;
            RT  qhy;
            RT  qhw;
            RT  rhx;
            RT  rhy;
            RT  rhw;

            dao.get( p, phx, phy, phw);
            dao.get( q, qhx, qhy, qhw);
            dao.get( r, rhx, rhy, rhw);

            RT  qhx_phx( qhx*phw - phx*qhw);
            RT  qhy_phy( qhy*phw - phy*qhw);    // denominator: qhw*phw

            RT  rhx_phx( rhx*phw - phx*rhw);
            RT  rhy_phy( rhy*phw - phy*rhw);    // denominator: rhw*phw
 
            RT  phw2( phw*phw);
            RT  qhw2( qhw*qhw);
            RT  rhw2( rhw*rhw);

            RT  p2( phx*phx + phy*phy);         // denominator: phw2

            RT  q2_p2( ( qhx*qhx + qhy*qhy) * phw2 - p2 * qhw2);
                                                // denominator: qhw2*phw2

            RT  r2_p2( ( rhx*rhx + rhy*rhy) * phw2 - p2 * rhw2);
                                                // denominator: rhw2*phw2

            center_hx = q2_p2*rhy_phy * rhw - r2_p2*qhy_phy * qhw;
            center_hy = r2_p2*qhx_phx * qhw - q2_p2*rhx_phx * rhw;
            center_hw = ( qhx_phx*rhy_phy - rhx_phx*qhy_phy)
                          * phw*qhw*rhw * RT( 2);
            sqr_rad   = sqr_dist( phx, phy, phw,
                                  center_hx, center_hy, center_hw);
        }

        // predicates
        CGAL_Bounded_side
        bounded_side( Point const& p) const
        {
            RT  phx;
            RT  phy;
            RT  phw;
            dao.get( p, phx, phy, phw);
            return( CGAL_static_cast( CGAL_Bounded_side,
                        CGAL_sign( sqr_dist( phx, phy, phw,
                                             center_hx, center_hy, center_hw)
                                   - sqr_rad)));
        }

        bool
        has_on_bounded_side( Point const& p) const
        {
            RT  phx;
            RT  phy;
            RT  phw;
            dao.get( p, phx, phy, phw);
            return( sqr_dist( phx, phy, phw,
                              center_hx, center_hy, center_hw) < sqr_rad);
        }

        bool
        has_on_boundary( Point const& p) const
        {
            RT  phx;
            RT  phy;
            RT  phw;
            dao.get( p, phx, phy, phw);
            return( sqr_dist( phx, phy, phw,
                              center_hx, center_hy, center_hw) == sqr_rad);
        }

        bool
        has_on_unbounded_side( Point const& p) const
        {
            RT  phx;
            RT  phy;
            RT  phw;
            dao.get( p, phx, phy, phw);
            return( sqr_rad < sqr_dist( phx, phy, phw,
                                        center_hx, center_hy, center_hw));
        }

        bool
        is_empty( ) const
        {
            return( CGAL_is_negative( sqr_rad));
        }

        bool
        is_degenerate( ) const
        {
            return( ! CGAL_is_positive( sqr_rad));
        }

        // additional operations for checking
        bool
        operator == (
            CGAL__Min_circle_2_adapterH2__Circle<_PT,_DA> const& c) const
        {
            return( ( center_hx*c.center_hw == c.center_hx*center_hw) &&
                    ( center_hy*c.center_hw == c.center_hy*center_hw) &&
                    ( sqr_rad  == c.sqr_rad ) );
        }

        Point
        center( ) const
        {
            Point  p;
            dao.set( p, center_hx, center_hy, center_hw);
            return( p);
        }

        Distance const&
        squared_radius( ) const
        {
            return( sqr_rad);
        }

        // I/O
        friend
        ostream&
        operator << ( ostream& os,
                      CGAL__Min_circle_2_adapterH2__Circle<_PT,_DA> const& c)
        {
            switch ( CGAL_get_mode( os)) {

              case CGAL_IO::PRETTY:
                os << "CGAL_Min_circle_2_adapterH2::Circle( "
                   << c.center_hx << ", "
                   << c.center_hy << ", "
                   << c.center_hw << ", "
                   << c.sqr_rad   << ')';
                break;

              case CGAL_IO::ASCII:
                os << c.center_hx << ' '
                   << c.center_hy << ' '
                   << c.center_hw << ' '
                   << c.sqr_rad;
                break;

              case CGAL_IO::BINARY:
                CGAL_write( os, c.center_hx);
                CGAL_write( os, c.center_hy);
                CGAL_write( os, c.center_hw);
                CGAL_write( os, c.sqr_rad);
                break;

              default:
                CGAL_optimisation_assertion_msg( false,
                                                "CGAL_get_mode( os) invalid!");
                break; }

            return( os);
        }

        friend
        istream&
        operator >> ( istream& is,
                      CGAL__Min_circle_2_adapterH2__Circle<_PT,_DA>& c)
        {
            switch ( CGAL_get_mode( is)) {

              case CGAL_IO::PRETTY:
                cerr << endl;
                cerr << "Stream must be in ascii or binary mode" << endl;
                break;

              case CGAL_IO::ASCII:
                is >> c.center_hx >> c.center_hy >> c.center_hw >> c.sqr_rad;
                break;

              case CGAL_IO::BINARY:
                CGAL_read( is, c.center_hx);
                CGAL_read( is, c.center_hy);
                CGAL_read( is, c.center_hw);
                CGAL_read( is, c.sqr_rad);
                break;

              default:
                CGAL_optimisation_assertion_msg( false,
                                                 "CGAL_IO::mode invalid!");
                break; }

            return( is);
        }
    };
@end

@! ============================================================================
@! Tests
@! ============================================================================

\clearpage
\section{Test}

We test \ccc{CGAL_Min_circle_2} with the traits class implementation
for optimisation algorithms, using exact arithmetic, i.e.\ Cartesian
representation with number type \ccc{CGAL_Quotient<CGAL_Gmpz>} and
homogeneous representation with number type \ccc{CGAL_Gmpz}.

@macro <Min_circle_2 test (includes and typedefs)> = @begin
    #include <CGAL/Cartesian.h>
    #include <CGAL/Homogeneous.h>
    #include <CGAL/Optimisation_traits_2.h>
    #include <CGAL/Min_circle_2.h>
    #include <CGAL/Min_circle_2_adapterC2.h>
    #include <CGAL/Min_circle_2_adapterH2.h>
    #include <CGAL/IO/Verbose_ostream.h>
    #include <assert.h>
    #include <string.h>
    #include <fstream.h>

    #ifdef CGAL_USE_LEDA
    #  include <CGAL/leda_integer.h>
       typedef  leda_integer			 Rt;
       typedef  CGAL_Quotient< leda_integer >	 Ft;
    #else
    #  include <CGAL/Gmpz.h>
       typedef  CGAL_Gmpz			 Rt;
       typedef  CGAL_Quotient< CGAL_Gmpz >	 Ft;
    #endif

    typedef  CGAL_Cartesian< Ft >		 RepC;
    typedef  CGAL_Homogeneous< Rt >		 RepH;
    typedef  CGAL_Optimisation_traits_2< RepC >	 TraitsC;
    typedef  CGAL_Optimisation_traits_2< RepH >	 TraitsH;
@end

The command line option \ccc{-verbose} enables verbose output.

@macro <Min_circle_2 test (verbose option)> = @begin
    bool  verbose = false;
    if ( ( argc > 1) && ( strcmp( argv[ 1], "-verbose") == 0)) {
        verbose = true;
        --argc;
        ++argv; }
@end

@! ----------------------------------------------------------------------------
@! Code Coverage
@! ----------------------------------------------------------------------------

\subsection{Code Coverage}

We call each function of class \ccc{CGAL_Min_circle_2<Traits>} at least
once to ensure code coverage.

@macro <Min_circle_2 test (code coverage)> = @begin
    cover_Min_circle_2( verbose, TraitsC(), Rt());
    cover_Min_circle_2( verbose, TraitsH(), Rt());
@end

@macro <Min_circle_2 test (code coverage test function)> = @begin
    template < class Traits, class RT >
    void
    cover_Min_circle_2( bool verbose, Traits const&, RT const&)
    {
        typedef  CGAL_Min_circle_2< Traits >  Min_circle;
        typedef  Min_circle::Point            Point;
        typedef  Min_circle::Circle           Circle;

        CGAL_Verbose_ostream verr( verbose);

        // generate `n' points at random
        const int    n = 20;
        CGAL_Random  random_x, random_y;
        Point        random_points[ n];
        int          i;
        verr << n << " random points from [0,128)^2:" << endl;
        for ( i = 0; i < n; ++i)
            random_points[ i] = Point( RT( random_x( 128)),
                                       RT( random_y( 128)));
        if ( verbose)
            for ( i = 0; i < n; ++i)
                cerr << i << ": " << random_points[ i] << endl;

        // cover code
        verr << endl << "default constructor...";
        {
            Min_circle  mc;
            bool  is_valid = mc.is_valid( verbose);
            bool  is_empty = mc.is_empty();
            assert( is_valid); 
            assert( is_empty);
        }

        verr << endl << "one point constructor...";
        {
            Min_circle  mc( random_points[ 0]);
            bool  is_valid      = mc.is_valid( verbose);
            bool  is_degenerate = mc.is_degenerate();
            assert( is_valid);
            assert( is_degenerate);
        }

        verr << endl << "two points constructor...";
        {
            Min_circle  mc( random_points[ 1],
                            random_points[ 2]);
            bool  is_valid = mc.is_valid( verbose);
            assert( is_valid);
            assert( mc.number_of_points() == 2);
        }

        verr << endl << "three points constructor...";
        {    
            Min_circle  mc( random_points[ 3],
                            random_points[ 4],
                            random_points[ 5]);
            bool  is_valid = mc.is_valid( verbose);
            assert( is_valid);
            assert( mc.number_of_points() == 3);
        }

        verr << endl << "Point* constructor...";
        Min_circle  mc( random_points, random_points+9);
        {
            Min_circle  mc2( random_points, random_points+9, true);
            bool  is_valid  = mc .is_valid( verbose);
            bool  is_valid2 = mc2.is_valid( verbose);
            assert( is_valid);
            assert( is_valid2);
            assert( mc .number_of_points() == 9);
            assert( mc2.number_of_points() == 9);
            assert( mc.circle() == mc2.circle());
        }

        verr << endl << "list<Point>::const_iterator constructor...";
        {
            Min_circle  mc1( mc.points_begin(), mc.points_end());
            Min_circle  mc2( mc.points_begin(), mc.points_end(), true);
            bool  is_valid1 = mc1.is_valid( verbose);
            bool  is_valid2 = mc2.is_valid( verbose);
            assert( is_valid1);
            assert( is_valid2);
            assert( mc1.number_of_points() == 9);
            assert( mc2.number_of_points() == 9);
            assert( mc.circle() == mc1.circle());
            assert( mc.circle() == mc2.circle());
        }

        verr << endl << "#points already called above.";

        verr << endl << "points access already called above.";

        verr << endl << "support points access...";
        {
            Point  support_point;
            Min_circle::Support_point_iterator
                iter( mc.support_points_begin());
            for ( i = 0; i < mc.number_of_support_points(); ++i, ++iter) {
                support_point = mc.support_point( i);
                assert( support_point == *iter); }
            Min_circle::Support_point_iterator
                end_iter( mc.support_points_end());
            assert( iter == end_iter);
        }

        verr << endl << "circle access already called above...";

        verr << endl << "in-circle predicates...";
        {
            Point              p;
            CGAL_Bounded_side  bounded_side;
            bool               has_on_bounded_side;
            bool               has_on_boundary;
            bool               has_on_unbounded_side;
            for ( i = 0; i < 9; ++i) {
                p = random_points[ i];
                bounded_side          = mc.bounded_side( p);
                has_on_bounded_side   = mc.has_on_bounded_side( p);
                has_on_boundary       = mc.has_on_boundary( p);
                has_on_unbounded_side = mc.has_on_unbounded_side( p);
            assert( bounded_side != CGAL_ON_UNBOUNDED_SIDE);
            assert( has_on_bounded_side || has_on_boundary);
            assert( ! has_on_unbounded_side); }
        }

        verr << endl << "is_... predicates already called above.";

        verr << endl << "single point insert...";
        mc.insert( random_points[ 9]);
        {
            bool  is_valid = mc.is_valid( verbose);
            assert( is_valid);
            assert( mc.number_of_points() == 10);
        }

        verr << endl << "Point* insert...";
        mc.insert( random_points+10, random_points+n);
        {
            bool  is_valid = mc.is_valid( verbose);
            assert( is_valid);
            assert( mc.number_of_points() == n);
        }

        verr << endl << "list<Point>::const_iterator insert...";
        {
            Min_circle  mc2;
            mc2.insert( mc.points_begin(), mc.points_end());
            bool  is_valid = mc2.is_valid( verbose);
            assert( is_valid);
            assert( mc2.number_of_points() == n);
            
            verr << endl << "clear...";
            mc2.clear();        
                  is_valid = mc2.is_valid( verbose);
            bool  is_empty = mc2.is_empty();
            assert( is_valid); 
            assert( is_empty);
        }

        verr << endl << "validity check already called several times.";

        verr << endl << "traits class access...";
        {
            Traits  traits( mc.traits());
        }

        verr << endl << "I/O...";
        {
            verr << endl << "  writing `test_Min_circle_2.ascii'...";
            ofstream os( "test_Min_circle_2.ascii");
            CGAL_set_ascii_mode( os);
            os << mc;
        }
        {
            verr << endl << "  writing `test_Min_circle_2.pretty'...";
            ofstream os( "test_Min_circle_2.pretty");
            CGAL_set_pretty_mode( os);
            os << mc;
        }
        {
            verr << endl << "  writing `test_Min_circle_2.binary'...";
            ofstream os( "test_Min_circle_2.binary");
            CGAL_set_binary_mode( os);
            os << mc;
        }
        {
            verr << endl << "  reading `test_Min_circle_2.ascii'...";
            Min_circle mc_in;
            ifstream is( "test_Min_circle_2.ascii");
            CGAL_set_ascii_mode( is);
            is >> mc_in;
            bool    is_valid = mc_in.is_valid( verbose);
            assert( is_valid);
            assert( mc_in.number_of_points() == n);
            assert( mc_in.circle() == mc.circle());
        }
        verr << endl;
    }
@end

@! ----------------------------------------------------------------------------
@! Adapters
@! ----------------------------------------------------------------------------

\subsection{Traits Class Adapters}

We define two point classes (one with Cartesian, one with homogeneous
representation) and corresponding data accessors.

@macro <Min_circle_2 test (point classes)> = @begin
    // 2D Cartesian point class
    class MyPointC2 {
      public:
        typedef  ::Ft  FT;
      private:
        FT _x;
        FT _y;
      public:
        MyPointC2( ) { }
        MyPointC2( FT const& x, FT const& y) : _x( x), _y( y) { }

        FT const&  x( ) const { return( _x); }
        FT const&  y( ) const { return( _y); }

        bool
        operator == ( MyPointC2 const& p) const
        {
            return( ( _x == p._x) && ( _y == p._y));
        }

        friend
        ostream&
        operator << ( ostream& os, MyPointC2 const& p)
        {
            return( os << p._x << ' ' << p._y);
        }

        friend
        istream&
        operator >> ( istream& is, MyPointC2& p)
        {
            return( is >> p._x >> p._y);
        }
    };

    // 2D Cartesian point class data accessor
    class MyPointC2DA {
      public:
        typedef  ::Ft  FT;

        FT const&  get_x( MyPointC2 const& p) const { return( p.x()); }
        FT const&  get_y( MyPointC2 const& p) const { return( p.y()); }

        void
        get( MyPointC2 const& p, FT& x, FT& y) const
        {
            x = get_x( p);
            y = get_y( p);
        }

        void
        set( MyPointC2& p, FT const& x, FT const& y) const
        {
            p = MyPointC2( x, y);
        }
    };


    // 2D homogeneous point class
    class MyPointH2 {
      public:
        typedef  ::Rt  RT;
      private:
        RT _hx;
        RT _hy;
        RT _hw;
      public:
        MyPointH2( ) { }
        MyPointH2( RT const& hx, RT const& hy, RT const& hw = RT( 1))
            : _hx( hx), _hy( hy), _hw( hw) { }

        RT const&  hx( ) const { return( _hx); }
        RT const&  hy( ) const { return( _hy); }
        RT const&  hw( ) const { return( _hw); }

        bool
        operator == ( MyPointH2 const& p) const
        {
            return( ( _hx*p._hw == p._hx*_hw) && ( _hy*p._hw == p._hy*_hw));
        }

        friend
        ostream&
        operator << ( ostream& os, MyPointH2 const& p)
        {
            return( os << p._hx << ' ' << p._hy << ' ' << p._hw);
        }

        friend
        istream&
        operator >> ( istream& is, MyPointH2& p)
        {
            return( is >> p._hx >> p._hy >> p._hw);
        }
    };

    // 2D homogeneous point class data accessor
    class MyPointH2DA {
      public:
        typedef  ::Rt  RT;

        RT const&  get_hx( MyPointH2 const& p) const { return( p.hx()); }
        RT const&  get_hy( MyPointH2 const& p) const { return( p.hy()); }
        RT const&  get_hw( MyPointH2 const& p) const { return( p.hw()); }

        void
        get( MyPointH2 const& p, RT& hx, RT& hy, RT& hw) const
        {
            hx = get_hx( p);
            hy = get_hy( p);
            hw = get_hw( p);
        }

        void
        set( MyPointH2& p, RT const& hx, RT const& hy, RT const& hw) const
        {
            p = MyPointH2( hx, hy, hw);
        }
    };
@end

To test the traits class adapters we use the code coverage test function.

@macro <Min_circle_2 test (adapters test)> = @begin
    typedef  CGAL_Min_circle_2_adapterC2< MyPointC2, MyPointC2DA >  AdapterC2;
    typedef  CGAL_Min_circle_2_adapterH2< MyPointH2, MyPointH2DA >  AdapterH2;
    cover_Min_circle_2( verbose, AdapterC2(), Rt());
    cover_Min_circle_2( verbose, AdapterH2(), Rt());
@end

@! ----------------------------------------------------------------------------
@! External Test Sets
@! ----------------------------------------------------------------------------

\subsection{External Test Sets}

In addition, some data files can be given as command line arguments.
A data file contains pairs of \ccc{int}s, namely the x- and
y-coordinates of a set of points. The first number in the file is the
number of points. A short description of the test set is given at the
end of each file.

@macro <Min_circle_2 test (external test sets)> = @begin
    while ( argc > 1) {

        typedef  CGAL_Min_circle_2< TraitsH >  Min_circle;
        typedef  Min_circle::Point             Point;
        typedef  Min_circle::Circle            Circle;

        CGAL_Verbose_ostream verr( verbose);

        // read points from file
        verr << endl << "input file: `" << argv[ 1] << "'" << flush;

        list<Point>  points;
        int          n, x, y;
        ifstream     in( argv[ 1]);
        in >> n;
        assert( in);
        for ( int i = 0; i < n; ++i) {
            in >> x >> y;
            assert( in);
            points.push_back( Point( x, y)); }

        // compute and check min_circle
        Min_circle  mc2( points.begin(), points.end());
        bool  is_valid = mc2.is_valid( verbose);
        assert( is_valid);

        // next file
        --argc;
        ++argv; }
@end

@! ==========================================================================
@! Files
@! ==========================================================================

\clearpage
\section{Files}

@! ----------------------------------------------------------------------------
@! Min_circle_2.h
@! ----------------------------------------------------------------------------

\subsection{Min\_circle\_2.h}

@file <include/CGAL/Min_circle_2.h> = @begin
    @<Min_circle_2 header>("include/CGAL/Min_circle_2.h")

    #ifndef CGAL_MIN_CIRCLE_2_H
    #define CGAL_MIN_CIRCLE_2_H

    // Class declaration
    // =================
    @<Min_circle_2 declaration>

    // Class interface
    // ===============
    // includes
    #ifndef CGAL_RANDOM_H
    #  include <CGAL/Random.h>
    #endif
    #ifndef CGAL_OPTIMISATION_ASSERTIONS_H
    #  include <CGAL/optimisation_assertions.h>
    #endif
    #ifndef CGAL_OPTIMISATION_MISC_H
    #  include <CGAL/optimisation_misc.h>
    #endif
    #ifndef CGAL_PROTECT_LIST_H
    #  include <list.h>
    #endif
    #ifndef CGAL_PROTECT_VECTOR_H
    #include <vector.h>
    #endif
    #ifndef CGAL_PROTECT_ALGO_H
    #include <algo.h>
    #endif
    #ifndef CGAL_PROTECT_IOSTREAM_H
    #include <iostream.h>
    #endif

    @<Min_circle_2 interface>

    // Function declarations
    // =====================
    // I/O
    // ---
    @<Min_circle_2 I/O operators declaration>

    #ifdef CGAL_CFG_NO_AUTOMATIC_TEMPLATE_INCLUSION
    #  include <CGAL/Min_circle_2.C>
    #endif

    #endif // CGAL_MIN_CIRCLE_2_H

    @<end of file line>
@end

@! ----------------------------------------------------------------------------
@! Min_circle_2.C
@! ----------------------------------------------------------------------------

\subsection{Min\_circle\_2.C}

@file <include/CGAL/Min_circle_2.C> = @begin
    @<Min_circle_2 header>("include/CGAL/Min_circle_2.C")

    // Class implementation (continued)
    // ================================
    // I/O
    // ---
    @<Min_circle_2 I/O operators>

    @<end of file line>
@end

@! ----------------------------------------------------------------------------
@! Optimisation_circle_2.h
@! ----------------------------------------------------------------------------

\subsection{Optimisation\_circle\_2.h}

@file <include/CGAL/Optimisation_circle_2.h> = @begin
    @<Optimisation_circle_2 header>("include/CGAL/Optimisation_circle_2.h")

    #ifndef CGAL_OPTIMISATION_CIRCLE_2_H
    #define CGAL_OPTIMISATION_CIRCLE_2_H

    // Class declaration
    // =================
    @<Optimisation_circle_2 declaration>

    // Class interface
    // ===============
    // includes
    #ifndef CGAL_POINT_2_H
    #  include <CGAL/Point_2.h>
    #endif
    #ifndef CGAL_BASIC_CONSTRUCTIONS_2_H
    #  include <CGAL/basic_constructions_2.h>
    #endif
    #ifndef CGAL_SQUARED_DISTANCE_2_H
    #  include <CGAL/squared_distance_2.h>
    #endif

    @<Optimisation_circle_2 interface>

    // Function declarations
    // =====================
    // I/O
    // ---
    @<Optimisation_circle_2 I/O operators declaration>

    #ifdef CGAL_CFG_NO_AUTOMATIC_TEMPLATE_INCLUSION
    #  include <CGAL/Optimisation_circle_2.C>
    #endif

    #endif // CGAL_OPTIMISATION_CIRCLE_2_H

    @<end of file line>
@end

@! ----------------------------------------------------------------------------
@! Optimisation_circle_2.C
@! ----------------------------------------------------------------------------

\subsection{Optimisation\_circle\_2.C}

@file <include/CGAL/Optimisation_circle_2.C> = @begin
    @<Optimisation_circle_2 header>("include/CGAL/Optimisation_circle_2.C")

    // Class implementation (continued)
    // ================================
    // includes
    #ifndef CGAL_OPTIMISATION_ASSERTIONS_H
    #  include <CGAL/optimisation_assertions.h>
    #endif

    // I/O
    // ---
    @<Optimisation_circle_2 I/O operators>

    @<end of file line>
@end

@! ----------------------------------------------------------------------------
@! Min_circle_2_adapterC2.h
@! ----------------------------------------------------------------------------

\subsection{Min\_circle\_2\_adapterC2.h}

@file <include/CGAL/Min_circle_2_adapterC2.h> = @begin
    @<Min_circle_2 header>("include/CGAL/Min_circle_2_adapterC2.h")

    #ifndef CGAL_MIN_CIRCLE_2_ADAPTERC2_H
    #define CGAL_MIN_CIRCLE_2_ADAPTERC2_H

    // Class declarations
    // ==================
    @<Min_circle_2_adapterC2 declarations>

    // Class interface and implementation
    // ==================================
    // includes
    #ifndef CGAL_BASIC_H
    #  include <CGAL/basic.h>
    #endif
    #ifndef CGAL_OPTIMISATION_ASSERTIONS_H
    #  include <CGAL/optimisation_assertions.h>
    #endif

    @<Min_circle_2_adapterC2 interface and implementation>

    // Nested type `Circle'
    @<Min_circle_2_adapterC2 nested type `Circle'>

    #endif // CGAL_MIN_CIRCLE_2_ADAPTERC2_H

    @<end of file line>
@end

@! ----------------------------------------------------------------------------
@! Min_circle_2_adapterH2.h
@! ----------------------------------------------------------------------------

\subsection{Min\_circle\_2\_adapterH2.h}

@file <include/CGAL/Min_circle_2_adapterH2.h> = @begin
    @<Min_circle_2 header>("include/CGAL/Min_circle_2_adapterH2.h")

    #ifndef CGAL_MIN_CIRCLE_2_ADAPTERH2_H
    #define CGAL_MIN_CIRCLE_2_ADAPTERH2_H

    // Class declarations
    // ==================
    @<Min_circle_2_adapterH2 declarations>

    // Class interface and implementation
    // ==================================
    // includes
    #ifndef CGAL_BASIC_H
    #  include <CGAL/basic.h>
    #endif
    #ifndef CGAL_OPTIMISATION_ASSERTIONS_H
    #  include <CGAL/optimisation_assertions.h>
    #endif

    @<Min_circle_2_adapterH2 interface and implementation>

    // Nested type `Circle'
    @<Min_circle_2_adapterH2 nested type `Circle'>

    #endif // CGAL_MIN_CIRCLE_2_ADAPTERH2_H

    @<end of file line>
@end

@! ----------------------------------------------------------------------------
@! test_Min_circle_2.C
@! ----------------------------------------------------------------------------

\subsection{test\_Min\_circle\_2.C}

@file <test/Optimisation/test_Min_circle_2.C> = @begin
    @<Min_circle_2 header>("test/optimisation/test_Min_circle_2.C")

    @<Min_circle_2 test (includes and typedefs)>

    // code coverage test function
    // ---------------------------
    @<Min_circle_2 test (code coverage test function)>

    // point classes for adapters test
    // -------------------------------
    @<Min_circle_2 test (point classes)>

    // main
    // ----
    int
    main( int argc, char* argv[])
    {
        // command line options
        // --------------------
        // option `-verbose'
        @<Min_circle_2 test (verbose option)>

        // code coverage
        // -------------
        @<Min_circle_2 test (code coverage)>

        // adapters test
        // -------------
        @<Min_circle_2 test (adapters test)>

        // external test sets
        // -------------------
        @<Min_circle_2 test (external test sets)>
    }

    @<end of file line>
@end

@! ----------------------------------------------------------------------------
@! File Header
@! ----------------------------------------------------------------------------

\subsection*{File Header}

@i ../file_header.awi
 
@macro <Min_circle_2 header>(1) many = @begin
    @<file header>("2D Smallest Enclosing Circle",@1,
                   "Optimisation/Min_circle_2",
                   "Sven Sch�nherr <sven@@inf.fu-berlin.de>",
                   "Bernd G�rtner",
		   "ETH Zurich (Bernd G�rtner <gaertner@@inf.ethz.ch>)",
                   "$Revision$","$Date$")
@end

@macro <Optimisation_circle_2 header>(1) many = @begin
    @<file header>("2D Optimisation Circle",@1,
                   "Optimisation/Min_circle_2",
                   "Sven Sch�nherr <sven@@inf.fu-berlin.de>",
                   "Bernd G�rtner",
		   "ETH Zurich (Bernd G�rtner <gaertner@@inf.ethz.ch>)",
                   "$Revision$","$Date$")
@end

@! ============================================================================
@! Bibliography
@! ============================================================================

\clearpage
\bibliographystyle{plain}
\bibliography{geom,cgal}

@! ===== EOF ==================================================================
